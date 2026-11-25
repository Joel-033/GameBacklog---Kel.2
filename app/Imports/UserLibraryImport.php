<?php

namespace App\Imports;

use App\Models\Game;
use App\Models\Platform;
use App\Models\UserGame;
use Illuminate\Support\Collection;
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

            // Cari game di katalog utama (lewati jika judul tidak ada di katalog)
            $game = Game::where('title', 'LIKE', $title)->first();
            if (!$game) {
                continue;
            }

            // Validasi Status (default: Backlog)
            $rawStatus = trim((string) ($row['status'] ?? 'Backlog'));
            $status = in_array($rawStatus, $validStatuses, true) ? $rawStatus : 'Backlog';

            // Cari Platform jika diisi
            $rawPlatform = trim((string) ($row['platform'] ?? ''));
            $platformId = null;
            if ($rawPlatform !== '' && $rawPlatform !== '-') {
                $platform = Platform::where('name', 'LIKE', $rawPlatform)->first();
                $platformId = $platform?->id;
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
}