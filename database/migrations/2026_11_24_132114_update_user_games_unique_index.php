
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_games', function (Blueprint $table) {

            // 1. Membuat UNIQUE INDEX baru terlebih dahulu
            $table->unique(
                ['user_id', 'game_id'],
                'user_game_unique'
            );

            // 2. Setelah index baru tersedia,
            // hapus UNIQUE INDEX lama
            $table->dropUnique('user_game_platform_unique');

        });
    }

    public function down(): void
    {
        Schema::table('user_games', function (Blueprint $table) {

            // 1. Mengembalikan UNIQUE INDEX lama
            $table->unique(
                ['user_id', 'game_id', 'platform_id'],
                'user_game_platform_unique'
            );

            // 2. Menghapus UNIQUE INDEX baru
            $table->dropUnique('user_game_unique');

        });
    }
};
