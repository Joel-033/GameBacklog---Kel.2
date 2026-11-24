<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Platform;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            'PC',
            'PlayStation 5',
            'PlayStation 4',
            'Xbox Series X/S',
            'Nintendo Switch',
        ];

        foreach ($platforms as $platform) {
            Platform::firstOrCreate([
                'name' => $platform,
            ]);
        }
    }
}