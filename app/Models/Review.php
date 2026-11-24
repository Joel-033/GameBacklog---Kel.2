<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'rating',
        'gameplay_rating',
        'graphics_rating',
        'story_rating',
        'title',
        'content',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'gameplay_rating' => 'decimal:1',
        'graphics_rating' => 'decimal:1',
        'story_rating' => 'decimal:1',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}