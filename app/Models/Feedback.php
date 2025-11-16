<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $primaryKey = 'feedback_id';

    protected $fillable = [
        'job_id',
        'given_by',
        'given_to',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id', 'job_id');
    }

    public function giver()
    {
        return $this->belongsTo(User::class, 'given_by', 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'given_to', 'user_id');
    }
}
