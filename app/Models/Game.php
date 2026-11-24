<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'rawg_id',
        'genre_id',
        'title',
        'developer',
        'publisher',
        'release_date',
        'description',
        'cover_image',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(
            Platform::class,
            'game_platform'
        )->withTimestamps();
    }

    public function userGames(): HasMany
    {
        return $this->hasMany(UserGame::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}