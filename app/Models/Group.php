<?php

namespace App\Models;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['tournament_id', 'name'];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
