<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_games', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('game_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('platform_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status')->default('Plan to Play');

            $table->unsignedInteger('playtime')->default(0);

            $table->string('priority')->nullable();

            $table->date('added_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['user_id', 'game_id', 'platform_id'],
                'user_game_platform_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_games');
    }
};
