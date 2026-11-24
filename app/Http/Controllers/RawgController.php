<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Game;
use App\Models\UserGame;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class RawgController extends Controller
{
    // 1. Mengambil data game dari RAWG API
    public function search()
    {
        $response = Http::get('https://api.rawg.io/api/games', [
            'key' => config('services.rawg.key'),
            'search' => 'elden ring',
            'page_size' => 5,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Request RAWG gagal',
                'status' => $response->status(),
                'error' => $response->json(),
            ], $response->status());
        }

        return $response->json();
    }


    // 2. Menampilkan katalog game RAWG
    public function catalog(Request $request)
    {
        $search = $request->query('search', '');

        $games = [];
        $error = null;

        if (trim($search) !== '') {

            $response = Http::get('https://api.rawg.io/api/games', [
                'key' => config('services.rawg.key'),
                'search' => $search,
                'page_size' => 12,
            ]);

            if ($response->failed()) {
                $error = 'Gagal mengambil data dari RAWG API.';
            } else {
                $games = $response->json('results', []);
            }
        }

        return view('rawg.catalog', compact(
            'games',
            'search',
            'error'
        ));
    }


    // 3. Menampilkan detail game RAWG
    public function detail($id)
    {
        $response = Http::get(
            "https://api.rawg.io/api/games/{$id}",
            [
                'key' => config('services.rawg.key'),
            ]
        );

        if ($response->failed()) {
            abort(
                $response->status() === 404 ? 404 : 502,
                'Gagal mengambil detail game dari RAWG.'
            );
        }

        $game = $response->json();

        return view('rawg.detail', compact('game'));
    }


    // 4. Menambahkan game ke Personal Library
    public function addToMyGames(Request $request, $id)
    {
        // 1. Mengambil user yang sedang login
        $user = $request->user();

        // 2. Mengambil detail game dari RAWG API
        $response = Http::get(
            "https://api.rawg.io/api/games/{$id}",
            [
                'key' => config('services.rawg.key'),
            ]
        );

        // 3. Memeriksa apakah request berhasil
        if ($response->failed()) {
            return back()->with(
                'error',
                'Gagal mengambil data game dari RAWG.'
            );
        }

        // 4. Mengubah respons JSON menjadi array PHP
        $rawgGame = $response->json();

        // 5. Memastikan data game valid
        if (empty($rawgGame['id']) || empty($rawgGame['name'])) {
            return back()->with(
                'error',
                'Data game tidak valid.'
            );
        }

        // 6. Menjalankan proses sinkronisasi menggunakan database transaction
        $wasAdded = DB::transaction(function () use ($rawgGame, $user) {

            // 7. Mengambil genre pertama dari RAWG
            $genreName = $rawgGame['genres'][0]['name'] ?? null;

            $genre = null;

            if ($genreName) {
                $genre = Genre::firstOrCreate([
                    'name' => $genreName,
                ]);
            }

            // 8. Mencari game berdasarkan RAWG ID
            $game = Game::firstOrNew([
                'rawg_id' => $rawgGame['id'],
            ]);

            // 9. Mengisi informasi game
            $game->fill([
                'genre_id' => $genre?->id,

                'title' => $rawgGame['name'],

                'developer' => collect($rawgGame['developers'] ?? [])
                    ->pluck('name')
                    ->implode(', ') ?: null,

                'publisher' => collect($rawgGame['publishers'] ?? [])
                    ->pluck('name')
                    ->implode(', ') ?: null,

                'release_date' => $rawgGame['released'] ?: null,

                'description' => $rawgGame['description_raw']
                    ?? strip_tags($rawgGame['description'] ?? ''),

                'cover_image' => $rawgGame['background_image'] ?? null,
            ]);

            // 10. Menyimpan game ke database lokal
            $game->save();

            // 11. Mengambil daftar platform dari RAWG
            $platformIds = [];

            foreach ($rawgGame['platforms'] ?? [] as $item) {

                $platformName = $item['platform']['name'] ?? null;

                if (!$platformName) {
                    continue;
                }

                // Mencari platform atau membuat platform baru
                $platform = Platform::firstOrCreate([
                    'name' => $platformName,
                ]);

                $platformIds[] = $platform->id;
            }

            // 12. Menghubungkan game dengan platform
            $game->platforms()->sync($platformIds);

            // 13. Memeriksa apakah game sudah ada di Personal Library
            $alreadyExists = UserGame::where('user_id', $user->id)
                ->where('game_id', $game->id)
                ->exists();

            // 14. Jika sudah ada, hentikan proses penambahan
            if ($alreadyExists) {
                return false;
            }

            // 15. Menambahkan game ke Personal Library
            UserGame::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'platform_id' => null,
                'status' => 'Backlog',
                'playtime' => 0,
                'priority' => null,
                'added_at' => now()->toDateString(),
            ]);

            // 16. Mengembalikan status berhasil ditambahkan
            return true;
        });

        // 17. Memberikan notifikasi sesuai hasil proses

        if (!$wasAdded) {
            return back()->with(
                'error',
                'Game sudah ada di Personal Library!'
            );
        }

        return back()->with(
            'success',
            'Game berhasil disinkronkan dan ditambahkan ke Personal Library!'
        );
    }
}
