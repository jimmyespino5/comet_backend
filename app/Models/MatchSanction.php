<?php

namespace App\Models;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerPayment;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchSanction extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'card_type',
        'minute',
        'fine_amount',
        'is_paid',
    ];

    protected $casts = [
        'minute' => 'integer',
        'fine_amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PlayerPayment::class);
    }
}
