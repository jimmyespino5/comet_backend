<?php

namespace App\Models;

use App\Models\Game;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    use HasFactory;

    protected $fillable = ['tournament_id', 'round_number', 'name'];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
