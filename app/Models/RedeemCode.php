<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemCode extends Model
{
    use HasFactory;

    protected $primaryKey = 'redeem_code_id';

    protected $fillable = [
        'code',
        'created_by',
        'claimed_by',
        'amount',
        'is_claimed',
        'claimed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_claimed' => 'boolean',
            'claimed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function claimer()
    {
        return $this->belongsTo(User::class, 'claimed_by', 'user_id');
    }

    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isPast();
    }

    public function canBeClaimed(): bool
    {
        return !$this->is_claimed && !$this->isExpired();
    }
}
