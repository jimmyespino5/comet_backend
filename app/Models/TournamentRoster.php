<?php

namespace App\Models;

use App\Models\Player;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_registration_id',
        'player_id',
        'jersey_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jersey_number' => 'integer',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(TournamentRegistration::class, 'tournament_registration_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
