<?php

namespace App\Models;

use App\Models\Goal;
use App\Models\Group;
use App\Models\MatchSanction;
use App\Models\Round;
use App\Models\TeamPayment;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'round_id',
        'group_id',
        'home_registration_id',
        'away_registration_id',
        'scheduled_at',
        'venue',
        'home_score',
        'away_score',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'home_score' => 'integer',
        'away_score' => 'integer',
    ];

    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function homeRegistration(): BelongsTo
    {
        return $this->belongsTo(TournamentRegistration::class, 'home_registration_id');
    }

    public function awayRegistration(): BelongsTo
    {
        return $this->belongsTo(TournamentRegistration::class, 'away_registration_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class, 'match_id');
    }

    public function sanctions(): HasMany
    {
        return $this->hasMany(MatchSanction::class, 'match_id');
    }

    public function teamPayments(): HasMany
    {
        return $this->hasMany(TeamPayment::class, 'match_id');
    }
}
