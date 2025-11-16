<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'reported_by',
        'reported_user',
        'description',
        'status',
    ];

    public function getRouteKeyName()
    {
        return 'report_id';
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_ON_REVIEW = 'on_review';
    public const STATUS_DONE = 'done';

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by', 'user_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user', 'user_id');
    }
}
