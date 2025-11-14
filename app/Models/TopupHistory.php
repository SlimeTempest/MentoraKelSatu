<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopupHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'history_id';

    protected $fillable = [
        'topup_id',
        'approved_by',
        'status_before',
        'status_after',
    ];

    public function topup()
    {
        return $this->belongsTo(Topup::class, 'topup_id', 'topup_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }
}

