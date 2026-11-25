<?php

namespace App\Exports;

use App\Models\Game;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GamesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Enumerable
    {
        return Game::with(['genre', 'platforms'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul Game',
            'Genre',
            'Platform',
            'Developer',
            'Publisher',
            'Tanggal Rilis',
            'Deskripsi',
        ];
    }

    public function map($game): array
    {
        return [
            $game->id,
            $game->title,
            $game->genre->name ?? '-',
            $game->platforms->pluck('name')->implode(', ') ?: '-',
            $game->developer ?? '-',
            $game->publisher ?? '-',
            $game->release_date ?? '-',
            $game->description ?? '-',
        ];
    }
}