<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gamebacklog.test'],
            [
                'name' => 'GameBacklog Admin',
                'password' => Hash::make('Admin12345'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'gamer@gamebacklog.test'],
            [
                'name' => 'Demo Gamer',
                'password' => Hash::make('Gamer12345'),
                'role' => 'user',
            ]
        );
    }
}