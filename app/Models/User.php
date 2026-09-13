<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Database\Factories\UserFactory;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Asegúrate de que esté esta línea
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles; // Añadimos esta

#[Fillable(['name', 'email', 'password','role_id',
        'last_name',
        'phone',
        'is_active',])]

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    
    use HasFactory, Notifiable;
    use HasApiTokens, HasRoles;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'delegate_id');
    }

    public function playerProfile(): HasOne
    {
        return $this->hasOne(Player::class);
    }
}
