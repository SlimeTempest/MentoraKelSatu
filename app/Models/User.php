<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avg_rating',
        'is_suspended',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'avg_rating' => 'float',
            'is_suspended' => 'boolean',
        ];
    }

    public function jobsCreated()
    {
        return $this->hasMany(Job::class, 'created_by', 'user_id');
    }

    public function jobsAssigned()
    {
        return $this->hasMany(Job::class, 'assigned_to', 'user_id');
    }

    public function activeAssignmentsCount(): int
    {
        return $this->jobsAssigned()
            ->where('status', Job::STATUS_PROGRESS)
            ->count();
    }
}
