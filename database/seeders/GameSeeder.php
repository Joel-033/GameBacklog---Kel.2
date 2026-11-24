<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            [
                'title' => 'Tekken 8',
                'genre' => 'Fighting',
                'developer' => 'Bandai Namco Studios',
                'publisher' => 'Bandai Namco Entertainment',
                'release_date' => '2024-01-26',
                'platforms' => ['PC', 'PlayStation 5', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Street Fighter 6',
                'genre' => 'Fighting',
                'developer' => 'Capcom',
                'publisher' => 'Capcom',
                'release_date' => '2023-06-02',
                'platforms' => ['PC', 'PlayStation 5', 'PlayStation 4', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Mortal Kombat 1',
                'genre' => 'Fighting',
                'developer' => 'NetherRealm Studios',
                'publisher' => 'Warner Bros. Games',
                'release_date' => '2023-09-19',
                'platforms' => ['PC', 'PlayStation 5', 'Nintendo Switch', 'Xbox Series X/S'],
            ],
            [
                'title' => 'DOOM Eternal',
                'genre' => 'FPS',
                'developer' => 'id Software',
                'publisher' => 'Bethesda Softworks',
                'release_date' => '2020-03-20',
                'platforms' => ['PC', 'PlayStation 4', 'Nintendo Switch', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Elden Ring',
                'genre' => 'RPG',
                'developer' => 'FromSoftware',
                'publisher' => 'Bandai Namco Entertainment',
                'release_date' => '2022-02-25',
                'platforms' => ['PC', 'PlayStation 5', 'PlayStation 4', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Cyberpunk 2077',
                'genre' => 'RPG',
                'developer' => 'CD Projekt Red',
                'publisher' => 'CD Projekt',
                'release_date' => '2020-12-10',
                'platforms' => ['PC', 'PlayStation 5', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Red Dead Redemption 2',
                'genre' => 'Adventure',
                'developer' => 'Rockstar Games',
                'publisher' => 'Rockstar Games',
                'release_date' => '2018-10-26',
                'platforms' => ['PC', 'PlayStation 4', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Marvel Spider-Man Remastered',
                'genre' => 'Action',
                'developer' => 'Insomniac Games',
                'publisher' => 'Sony Interactive Entertainment',
                'release_date' => '2022-08-12',
                'platforms' => ['PC', 'PlayStation 5'],
            ],
            [
                'title' => 'God of War',
                'genre' => 'Action',
                'developer' => 'Santa Monica Studio',
                'publisher' => 'Sony Interactive Entertainment',
                'release_date' => '2018-04-20',
                'platforms' => ['PC', 'PlayStation 4'],
            ],
            [
                'title' => 'Horizon Forbidden West',
                'genre' => 'Adventure',
                'developer' => 'Guerrilla Games',
                'publisher' => 'Sony Interactive Entertainment',
                'release_date' => '2022-02-18',
                'platforms' => ['PC', 'PlayStation 5'],
            ],
            [
                'title' => 'Forza Horizon 5',
                'genre' => 'Racing',
                'developer' => 'Playground Games',
                'publisher' => 'Xbox Game Studios',
                'release_date' => '2021-11-09',
                'platforms' => ['PC', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Mario Kart 8 Deluxe',
                'genre' => 'Racing',
                'developer' => 'Nintendo',
                'publisher' => 'Nintendo',
                'release_date' => '2017-04-28',
                'platforms' => ['Nintendo Switch'],
            ],
            [
                'title' => 'The Sims 4',
                'genre' => 'Simulation',
                'developer' => 'Maxis',
                'publisher' => 'Electronic Arts',
                'release_date' => '2014-09-02',
                'platforms' => ['PC', 'PlayStation 4', 'Xbox Series X/S'],
            ],
            [
                'title' => 'The Witcher 3: Wild Hunt',
                'genre' => 'RPG',
                'developer' => 'CD Projekt Red',
                'publisher' => 'CD Projekt',
                'release_date' => '2015-05-19',
                'platforms' => ['PC', 'PlayStation 4', 'Nintendo Switch', 'Xbox Series X/S'],
            ],
            [
                'title' => 'Resident Evil 4',
                'genre' => 'Action',
                'developer' => 'Capcom',
                'publisher' => 'Capcom',
                'release_date' => '2023-03-24',
                'platforms' => ['PC', 'PlayStation 5', 'PlayStation 4', 'Xbox Series X/S'],
            ],
        ];

        foreach ($games as $item) {

            $genre = Genre::where('name', $item['genre'])->first();

            $game = Game::firstOrCreate(
                [
                    'title' => $item['title'],
                ],
                [
                    'genre_id' => $genre->id,
                    'developer' => $item['developer'],
                    'publisher' => $item['publisher'],
                    'release_date' => $item['release_date'],
                ]
            );

            $platformIds = Platform::whereIn(
                'name',
                $item['platforms']
            )->pluck('id')->toArray();

            $game->platforms()->syncWithoutDetaching($platformIds);
        }
    }
}