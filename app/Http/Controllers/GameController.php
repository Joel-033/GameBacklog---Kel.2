<?php

namespace App\Http\Controllers;

use App\Models\UserGame;
use App\Models\Game;
use App\Exports\GamesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GameController extends Controller
{
    /**
     * Display a listing of the resource (dengan Search, Filter Genre, dan Sort By).
     */
    public function index(Request $request)
    {
        $search  =$request->query('search');
        $genreId =$request->query('genre');
        $sort    =$request->query('sort', 'latest');

        $query = Game::with('genre')
            ->withCount('userGames')
            ->when($search, function ($q,$search) {
                $q->where('title', 'LIKE', "\%{$search}%");
            })
            ->when($genreId, function ($q,$genreId) {
                $q->where('genre_id',$genreId);
            });

        switch ($sort) {
            case 'newest':
                $query->orderByDesc('release_date');
                break;
            case 'popular':
                $query->orderByDesc('user_games_count')->orderBy('title');
                break;
            case 'top_rated':
                // Mengurutkan berdasarkan koleksi dengan prioritas High terbanyak lalu terbaru
                $query->withCount(['userGames as high_priority_count' => function ($q) {$q->where('priority', 'High');
                }])->orderByDesc('high_priority_count')->orderByDesc('user_games_count');
                break;
            case 'latest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $games = $query->paginate(12)->withQueryString();$genres = Genre::orderBy('name')->get();

        return view('games.index', compact('games', 'genres'));
    }

    /**
     * Live Search Autocomplete (JSON) untuk halaman katalog.
     */
    public function liveSearch(Request $request)
    {
        $keyword = trim((string)$request->query('q', ''));

        if ($keyword === '') {
            return response()->json([]);
        }

        $results = Game::with('genre')
            ->where('title', 'LIKE', "%{$keyword}%")
            ->orderBy('title')
            ->take(6)
            ->get()
            ->map(function ($game) {$coverUrl = null;
                if ($game->cover_image) {
                    $coverUrl = Str::startsWith($game->cover_image, ['http://', 'https://'])
                        ? $game->cover_image
                        : asset('storage/' . $game->cover_image);
                }

                return [
                    'id'           => $game->id,
                    'title'        => $game->title,
                    'genre'        => $game->genre->name ?? 'General',
                    'release_year' => $game->release_date ? Carbon::parse($game->release_date)->format('Y') : '-',
                    'cover'        => $coverUrl,
                    'url'          => route('games.show', $game->id),
                ];
            });

        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::all();$platforms = Platform::all();

        return view('games.create', compact('genres', 'platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated =$request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'developer' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'release_date' => 'required|date',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'exists:platforms,id',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $platformIds =$validated['platforms'];
        unset($validated['platforms']);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] =$request->file('cover_image')
                ->store('covers', 'public');
        }

        $game = Game::create($validated);
        $game->platforms()->sync($platformIds);

        return redirect()->route('games.index')
            ->with('success', 'Game berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $game = Game::with(['genre', 'platforms'])
            ->findOrFail($id);

        return view('games.show', compact('game'));
    }

    public function addToLibrary(Request $request, string$id)
    {
        $game = Game::findOrFail($id);
        $user =$request->user();

        $alreadyExists = UserGame::where('user_id',$user->id)
            ->where('game_id', $game->id)
            ->exists();

        if ($alreadyExists) {
            return back()->with('error', 'Game sudah ada di Personal Library kamu!');
        }

        UserGame::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'platform_id' => null,
            'status' => 'Backlog',
            'playtime' => 0,
            'priority' => null,
            'added_at' => now()->toDateString(),
        ]);

        return back()->with('success', 'Game berhasil ditambahkan ke Personal Library!');
    }

    public function myLibrary(Request $request)
    {
        $userGames =$request->user()
            ->userGames()
            ->with([
                'game.genre',
                'game.platforms',
                'platform'
            ])
            ->latest()
            ->paginate(12);

        // --- 1. HORIZONTAL BAR CHART (Top 5 Playtime) ---
        $topPlayedGames =$request->user()
            ->userGames()
            ->with('game')
            ->where('playtime', '>', 0)
            ->orderByDesc('playtime')
            ->take(5)
            ->get();

        $chartLabels =$topPlayedGames->map(fn($item) =>$item->game->title)->values();
        $chartData   =$topPlayedGames->pluck('playtime')->values();

        // --- 2. DOUGHNUT CHART (Distribusi Status) ---
        $statusCounts =$request->user()
            ->userGames()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels =$statusCounts->keys()->values();
        $statusData   =$statusCounts->values();
        // ---------------------------------------------

        return view('games.library', compact(
            'userGames',
            'chartLabels',
            'chartData',
            'statusLabels',
            'statusData'
        ));
    }

    public function editLibrary(string $id, Request$request)
    {
        $userGame =$request->user()
            ->userGames()
            ->with('game')
            ->findOrFail($id);

        $platforms = Platform::all();

        return view('games.library-edit', compact('userGame', 'platforms'));
    }

    public function updateLibrary(Request $request, string$id)
    {
        $userGame =$request->user()
            ->userGames()
            ->findOrFail($id);

        $validated =$request->validate([
            'status' => 'required|in:Plan to Play,Backlog,Playing,Completed,Dropped',
            'platform_id' => 'nullable|exists:platforms,id',
            'playtime' => 'required|integer|min:0',
            'priority' => 'nullable|in:Low,Medium,High',
        ]);

        $userGame->update($validated);

        return redirect()->route('games.library')
            ->with('success', 'Personal Library berhasil diperbarui!');
    }

    public function removeFromLibrary(string $id)
    {
        $userGame = auth()->user()
            ->userGames()
            ->findOrFail($id);

        $userGame->delete();

        return redirect()->route('games.library')
            ->with('success', 'Game berhasil dihapus dari Personal Library!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $game = Game::with('platforms')->findOrFail($id);

        $genres = Genre::all();$platforms = Platform::all();

        return view('games.edit', compact('game', 'genres', 'platforms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string$id)
    {
        $game = Game::findOrFail($id);

        $validated =$request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'developer' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'release_date' => 'required|date',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'exists:platforms,id',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $platformIds =$validated['platforms'];
        unset($validated['platforms']);

        $oldCover =$game->cover_image;

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] =$request->file('cover_image')
                ->store('covers', 'public');
        }

        $game->update($validated);
        $game->platforms()->sync($platformIds);

        if ($request->hasFile('cover_image') &&$oldCover) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('games.index')
            ->with('success', 'Game berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $game = Game::findOrFail($id);
        $coverImage =$game->cover_image;

        $game->platforms()->detach();$game->delete();

        if ($coverImage) {
            Storage::disk('public')->delete($coverImage);
        }

        return redirect()->route('games.index')
            ->with('success', 'Game berhasil dihapus!');
    }

    public function exportExcel()
    {
        return Excel::download(new GamesExport, 'katalog-game.xlsx');
    }
}