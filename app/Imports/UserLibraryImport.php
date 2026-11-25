<?php

namespace App\Imports;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\UserGame;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserLibraryImport implements ToCollection, WithHeadingRow
{
    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows): void
    {
        $validStatuses = ['Plan to Play', 'Backlog', 'Playing', 'Completed', 'Dropped'];
        $validPriorities = ['Low', 'Medium', 'High'];

        foreach ($rows as $row) {
            $title = trim((string) ($row['judul_game'] ?? $row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            // Cari game di katalog utama; jika belum ada, otomatis buat baru & ambil cover dari RAWG API
            $game = Game::where('title', 'LIKE', $title)->first();
            if (!$game) {
                $defaultGenre = Genre::firstOrCreate(['name' => 'General']);
                $coverImage = $this->fetchCoverFromRawg($title);

                $game = Game::create([
                    'title'        => $title,
                    'genre_id'     => $defaultGenre->id,
                    'developer'    => '-',
                    'publisher'    => '-',
                    'release_date' => now()->toDateString(),
                    'cover_image'  => $coverImage,
                ]);
            }

            // Validasi Status (default: Backlog)
            $rawStatus = trim((string) ($row['status'] ?? 'Backlog'));
            $status = in_array($rawStatus, $validStatuses, true) ? $rawStatus : 'Backlog';

            // Cari atau buat Platform jika diisi (misalnya Android / iOS)
            $rawPlatform = trim((string) ($row['platform'] ?? ''));
            $platformId = null;
            if ($rawPlatform !== '' && $rawPlatform !== '-') {
                $platform = Platform::where('name', 'LIKE', $rawPlatform)->first()
                    ?? Platform::create(['name' => $rawPlatform]);
                $platformId = $platform->id;
            }

            // Playtime (angka jam)
            $playtime = max(0, (int) ($row['playtime_jam'] ?? $row['playtime'] ?? 0));

            // Validasi Priority
            $rawPriority = trim((string) ($row['priority'] ?? ''));
            $priority = in_array($rawPriority, $validPriorities, true) ? $rawPriority : null;

            // Update jika sudah ada di library user, atau buat baru jika belum ada
            UserGame::updateOrCreate(
                [
                    'user_id' => $this->userId,
                    'game_id' => $game->id,
                ],
                [
                    'platform_id' => $platformId,
                    'status'      => $status,
                    'playtime'    => $playtime,
                    'priority'    => $priority,
                    'added_at'    => now()->toDateString(),
                ]
            );
        }
    }

    private function fetchCoverFromRawg(string $title): ?string
    {
        $apiKey = env('RAWG_API_KEY') ?: config('services.rawg.key');

        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get('https://api.rawg.io/api/games', [
                'key'       => $apiKey,
                'search'    => $title,
                'page_size' => 1,
            ]);

            if ($response->successful()) {
                return $response->json('results.0.background_image');
            }
        } catch (\Exception $e) {
            // Tetap lanjut import jika API lambat/gagal
        }

        return null;
    }
}