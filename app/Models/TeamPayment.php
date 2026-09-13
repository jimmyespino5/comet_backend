<?php

namespace App\Models;

use App\Models\Game;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_registration_id',
        'match_id',
        'concept',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(TournamentRegistration::class, 'tournament_registration_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'match_id');
    }
}
