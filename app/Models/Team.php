<?php

namespace App\Models;

use App\Models\Goal;
use App\Models\MatchSanction;
use App\Models\TournamentRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'delegate_id',
        'name',
        'logo_path',
        'primary_color',
        'secondary_color',
    ];

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegate_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
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
