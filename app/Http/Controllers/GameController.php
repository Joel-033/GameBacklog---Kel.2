<?php

namespace App\Http\Controllers;

use App\Models\UserGame;
use App\Models\Game;
use Illuminate\Support\Facades\Http;
use App\Exports\GamesExport;
use App\Imports\GamesImport;
use App\Exports\UserLibraryExport;
use App\Imports\UserLibraryImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Genre;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $games = Game::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'LIKE', "%{$search}%");
            })
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

            // Otomatis lengkapi cover_image dari RAWG API untuk game di halaman ini yang belum punya cover
        $apiKey = env('RAWG_API_KEY') ?: config('services.rawg.key');
        if ($apiKey) {
            foreach ($games as $gameItem) {
                if (empty($gameItem->cover_image)) {
                    try {
                        $res = Http::timeout(4)->get('https://api.rawg.io/api/games', [
                            'key'       => $apiKey,
                            'search'    => $gameItem->title,
                            'page_size' => 1,
                        ]);

                        if ($res->successful() && ($img = $res->json('results.0.background_image'))) {
                            $gameItem->update(['cover_image' => $img]);
                        }
                    } catch (\Exception $e) {
                        // Abaikan jika API timeout
                    }
                }
            }
        }

        return view('games.index', compact('games'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $genres = Genre::all();
        $platforms = Platform::all();

        return view('games.create', compact('genres', 'platforms'));
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // Validasi data yang dikirim dari form
        $validated = $request->validate([
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

        // Mengambil ID platform yang dipilih
        $platformIds = $validated['platforms'];

        // Menghapus platforms dari data utama
        unset($validated['platforms']);

        // Upload cover image jika tersedia
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        // Menyimpan data game
        $game = Game::create($validated);

        // Menyimpan relasi platform ke tabel pivot
        $game->platforms()->sync($platformIds);

        // Kembali ke katalog game
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

    public function addToLibrary(Request $request, string $id)
    {
        // Mencari game berdasarkan ID
        $game = Game::findOrFail($id);

        // Mengambil user yang sedang login
        $user = $request->user();

        // Memastikan game belum ada di library user
        $alreadyExists = UserGame::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->exists();

        if ($alreadyExists) {
            return back()->with('error', 'Game sudah ada di Personal Library kamu!');
        }

        // Menyimpan game ke Personal Library
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
        // Mengambil koleksi game milik user yang sedang login
        $userGames = $request->user()
            ->userGames()
            ->with([
                'game.genre',
                'game.platforms',
                'platform'
            ])
            ->latest()
            ->paginate(12);

        // Mengirim data ke halaman Personal Library
        return view('games.library', compact('userGames'));
    }

    public function editLibrary(string $id, Request $request)
    {
        // Mengambil game dari koleksi user yang sedang login
        $userGame = $request->user()
            ->userGames()
            ->with('game')
            ->findOrFail($id);

        // Mengambil daftar platform untuk pilihan
        $platforms = Platform::all();

        return view('games.library-edit', compact('userGame', 'platforms'));
    }


    public function updateLibrary(Request $request, string $id)
    {
        // Memastikan data yang diedit milik user yang sedang login
        $userGame = $request->user()
            ->userGames()
            ->findOrFail($id);

        // Memvalidasi input
        $validated = $request->validate([
            'status' => 'required|in:Plan to Play,Backlog,Playing,Completed,Dropped',
            'platform_id' => 'nullable|exists:platforms,id',
            'playtime' => 'required|integer|min:0',
            'priority' => 'nullable|in:Low,Medium,High',
        ]);

        // Memperbarui data koleksi
        $userGame->update($validated);

        return redirect()->route('games.library')
            ->with('success', 'Personal Library berhasil diperbarui!');
    }

    public function removeFromLibrary(string $id)
    {
        // Mencari game di Personal Library milik user yang sedang login
        $userGame = auth()->user()
            ->userGames()
            ->findOrFail($id);

        // Menghapus data dari Personal Library
        $userGame->delete();

        // Kembali ke halaman Personal Library
        return redirect()->route('games.library')
            ->with('success', 'Game berhasil dihapus dari Personal Library!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $game = Game::with('platforms')->findOrFail($id);

        $genres = Genre::all();
        $platforms = Platform::all();

        return view('games.edit', compact('game', 'genres', 'platforms'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        // Mencari game berdasarkan ID
        $game = Game::findOrFail($id);

        // Validasi data dari form edit
        $validated = $request->validate([
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

        // Mengambil ID platform yang dipilih
        $platformIds = $validated['platforms'];

        // Menghapus platforms dari data utama
        unset($validated['platforms']);

        // Menyimpan lokasi cover lama
        $oldCover = $game->cover_image;

        // Upload cover baru jika pengguna memilih gambar
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        // Memperbarui data game
        $game->update($validated);

        // Memperbarui relasi platform
        $game->platforms()->sync($platformIds);

        // Menghapus cover lama jika diganti
        if ($request->hasFile('cover_image') && $oldCover) {
            Storage::disk('public')->delete($oldCover);
        }

        // Kembali ke katalog game
        return redirect()->route('games.index')
            ->with('success', 'Game berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        // 1. Mengambil data game berdasarkan ID
        $game = Game::findOrFail($id);

        // 2. Menyimpan path cover sebelum data dihapus
        $coverImage = $game->cover_image;

        // 3. Menghapus relasi game dengan platform
        $game->platforms()->detach();

        // 4. Menghapus data game dari database
        $game->delete();

        // 5. Menghapus file cover jika tersedia
        if ($coverImage) {
            Storage::disk('public')->delete($coverImage);
        }

        // 6. Kembali ke halaman daftar game
        return redirect()->route('games.index')
            ->with('success', 'Game berhasil dihapus!');
    }

public function exportExcel()
    {
        return Excel::download(new GamesExport, 'katalog-game.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(new GamesImport, $request->file('file'));

        return redirect()->route('games.index')
            ->with('success', 'Data katalog game berhasil diimpor dari Excel!');
    }

    public function exportLibrary(Request $request)
    {
        return Excel::download(
            new UserLibraryExport($request->user()->id),
            'my-personal-library.xlsx'
        );
    }

    public function importLibrary(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(
            new UserLibraryImport($request->user()->id),
            $request->file('file')
        );

        return redirect()->route('games.library')
            ->with('success', 'Koleksi Personal Library berhasil diimpor dari Excel!');
    }

}
