<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $primaryKey = 'job_id';

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'assigned_to',
        'deadline',
        'status',
        'price',
    ];

    protected $casts = [
        'deadline' => 'date',
        'price' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'belum_diambil';
    public const STATUS_PROGRESS = 'on_progress';
    public const STATUS_DONE = 'selesai';
    public const STATUS_EXPIRED = 'kadaluarsa';

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'job_categories', 'job_id', 'category_id')
            ->withTimestamps();
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}

