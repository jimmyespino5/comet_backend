<?php

namespace App\Models;

use App\Models\Game;
use App\Models\Group;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamPayment;
use App\Models\Tournament;
use App\Models\TournamentRoster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TournamentRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'team_id',
        'group_id',
        'registered_at',
        'payment_status',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function roster(): HasMany
    {
        return $this->hasMany(TournamentRoster::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'tournament_rosters')
                    ->withPivot('jersey_number', 'is_active')
                    ->withTimestamps();
    }

    public function homeGames(): HasMany
    {
        return $this->hasMany(Game::class, 'home_registration_id');
    }

    public function awayGames(): HasMany
    {
        return $this->hasMany(Game::class, 'away_registration_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TeamPayment::class);
    }


    
}
