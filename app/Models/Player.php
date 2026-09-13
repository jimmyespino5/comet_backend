<?php

namespace App\Models;

use App\Models\Goal;
use App\Models\MatchSanction;
use App\Models\TournamentRegistration;
use App\Models\TournamentRoster;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dni',
        'first_name',
        'last_name',
        'birth_date',
        'photo_path',
        'position',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rosters(): HasMany
    {
        return $this->hasMany(TournamentRoster::class);
    }

    public function tournamentRegistrations(): BelongsToMany
    {
        return $this->belongsToMany(TournamentRegistration::class, 'tournament_rosters')
                    ->withPivot('jersey_number', 'is_active')
                    ->withTimestamps();
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function sanctions(): HasMany
    {
        return $this->hasMany(MatchSanction::class);
    }
}
