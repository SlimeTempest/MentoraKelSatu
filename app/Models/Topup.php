<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    use HasFactory;

    protected $primaryKey = 'topup_id';

    protected $fillable = [
        'user_id',
        'amount',
        'bukti_pembayaran',
        'rekening_tujuan',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function histories()
    {
        return $this->hasMany(TopupHistory::class, 'topup_id', 'topup_id');
    }

    public function approver()
    {
        return $this->hasOneThrough(
            User::class,
            TopupHistory::class,
            'topup_id',
            'user_id',
            'topup_id',
            'approved_by'
        )->where('topup_histories.status_after', self::STATUS_APPROVED);
    }
}

