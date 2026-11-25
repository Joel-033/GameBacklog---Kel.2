<?php

namespace App\Exports;

use App\Models\UserGame;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserLibraryExport implements FromCollection, WithHeadings, WithMapping
{
    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function collection(): Enumerable
    {
        return UserGame::with(['game', 'platform'])
            ->where('user_id', $this->userId)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Judul Game',
            'Status',
            'Platform',
            'Playtime (Jam)',
            'Priority',
        ];
    }

    public function map($userGame): array
    {
        return [
            $userGame->game->title ?? '-',
            $userGame->status,
            $userGame->platform->name ?? '-',
            $userGame->playtime,
            $userGame->priority ?? '-',
        ];
    }
}