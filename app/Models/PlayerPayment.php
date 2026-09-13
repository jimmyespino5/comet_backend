<?php

namespace App\Models;

use App\Models\MatchSanction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_sanction_id',
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

    public function sanction(): BelongsTo
    {
        return $this->belongsTo(MatchSanction::class, 'match_sanction_id');
    }
}
