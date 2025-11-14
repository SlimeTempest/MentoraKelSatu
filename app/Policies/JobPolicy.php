<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    /**
     * Determine whether the user can update the job.
     */
    public function update(User $user, Job $job): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $job->created_by === $user->user_id
            && $job->status === Job::STATUS_PENDING;
    }

    /**
     * Determine whether the user can delete the job.
     */
    public function delete(User $user, Job $job): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $job->created_by === $user->user_id
            && $job->status === Job::STATUS_PENDING;
    }
}

