<?php

namespace App\Models;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'minute',
        'type',
    ];

    protected $casts = [
        'minute' => 'integer',
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
}
