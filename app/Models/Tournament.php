<?php

namespace App\Models;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'edition',
        'start_date',
        'end_date',
        'entry_fee',
        'referee_fee_per_match',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'entry_fee' => 'decimal:2',
        'referee_fee_per_match' => 'decimal:2',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }
}
