<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Facades\DB;

class JobExpirationService
{
    /**
     * Proses expired jobs dan kembalikan saldo ke creator
     * 
     * @return int Jumlah job yang di-expire
     */
    public function expireJobs(): int
    {
        // Cari job yang sudah melewati deadline dan belum selesai
        $expiredJobs = Job::whereIn('status', [
            Job::STATUS_PENDING,
            Job::STATUS_PROGRESS
        ])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->with(['creator', 'assignee'])
            ->get();

        $expiredCount = 0;

        foreach ($expiredJobs as $job) {
            DB::transaction(function () use ($job, &$expiredCount) {
                // Update status job menjadi expired
                $job->update(['status' => Job::STATUS_EXPIRED]);

                // Kembalikan saldo ke creator
                // Jika job belum diambil (PENDING), kembalikan full saldo
                // Jika job sudah diambil (PROGRESS), kembalikan full saldo (worker tidak dapat bayaran karena expired)
                if ($job->creator) {
                    $job->creator->increment('balance', $job->price);
                }

                $expiredCount++;
            });
        }

        return $expiredCount;
    }
}

