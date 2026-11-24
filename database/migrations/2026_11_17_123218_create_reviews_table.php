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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('game_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('rating', 3, 1);

            $table->decimal('gameplay_rating', 3, 1)
                ->nullable();

            $table->decimal('graphics_rating', 3, 1)
                ->nullable();

            $table->decimal('story_rating', 3, 1)
                ->nullable();

            $table->string('title')->nullable();

            $table->longText('content')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
