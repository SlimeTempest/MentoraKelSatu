<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Feedback Model
 * 
 * BEST PRACTICES:
 * - Definisi relasi yang jelas (job, giver, receiver)
 * - Casts untuk tipe data yang konsisten
 * - Accessor untuk format data jika diperlukan
 */
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

    /**
     * BEST PRACTICE: Casts untuk memastikan tipe data konsisten
     */
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * BEST PRACTICE: Mutator untuk trim whitespace dari comment
     */
    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = $value ? trim($value) : null;
    }

    /**
     * Relasi ke Job
     * 
     * BEST PRACTICE: Definisi relasi yang jelas dengan foreign key
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id', 'job_id');
    }

    /**
     * Relasi ke User yang memberikan feedback (giver)
     */
    public function giver()
    {
        return $this->belongsTo(User::class, 'given_by', 'user_id');
    }

    /**
     * Relasi ke User yang menerima feedback (receiver/worker)
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'given_to', 'user_id');
    }

    /**
     * Scope untuk filter feedback berdasarkan rating
     * 
     * BEST PRACTICE: Query scope untuk reusability
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope untuk filter feedback yang memiliki komentar
     */
    public function scopeWithComment($query)
    {
        return $query->whereNotNull('comment')->where('comment', '!=', '');
    }
}
