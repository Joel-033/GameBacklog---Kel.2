<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Action',
            'Adventure',
            'Fighting',
            'RPG',
            'FPS',
            'Racing',
            'Simulation',
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate([
                'name' => $genre,
            ]);
        }
    }
}