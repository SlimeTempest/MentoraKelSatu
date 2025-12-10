<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobAssignmentController extends Controller
{
    public function take(Request $request, Job $job)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return back()->withErrors(['job' => 'Admin tidak bisa mengambil pekerjaan.']);
        }

        if ($job->created_by === $user->user_id) {
            return back()->withErrors(['job' => 'Kamu tidak bisa mengambil job buatan sendiri.']);
        }

        // Validasi sebelum transaction untuk early return
        if ($job->status !== Job::STATUS_PENDING) {
            return back()->withErrors(['job' => 'Job sudah tidak tersedia.']);
        }

        $activeAssignments = $user->jobsAssigned()
            ->where('status', Job::STATUS_PROGRESS)
            ->count();

        if ($activeAssignments >= 2) {
            return back()->withErrors(['job' => 'Kamu hanya bisa mengerjakan maksimal 2 job sekaligus.']);
        }

        // Gunakan DB transaction dengan lock untuk mencegah race condition
        // Lock job row untuk memastikan hanya satu user yang bisa mengambil
        try {
            DB::transaction(function () use ($job, $user) {
                // Reload job dengan lock untuk mencegah concurrent updates
                $jobLocked = Job::lockForUpdate()->find($job->job_id);
                
                if (!$jobLocked) {
                    throw new \Exception('Job tidak ditemukan.');
                }

                // Double check status setelah lock (penting untuk race condition)
                if ($jobLocked->status !== Job::STATUS_PENDING) {
                    throw new \Exception('Job sudah tidak tersedia.');
                }

                // Update job
                $jobLocked->update([
                    'assigned_to' => $user->user_id,
                    'status' => Job::STATUS_PROGRESS,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['job' => $e->getMessage()]);
        }

        return back()->with('status', 'Job berhasil diambil. Selamat bekerja!');
    }

    public function complete(Request $request, Job $job)
    {
        $user = $request->user();

        if ($job->assigned_to !== $user->user_id && $user->role !== 'admin') {
            return back()->withErrors(['job' => 'Kamu tidak berhak menyelesaikan job ini.']);
        }

        if ($job->status !== Job::STATUS_PROGRESS) {
            return back()->withErrors(['job' => 'Job ini belum dalam status pengerjaan.']);
        }

        // Gunakan DB transaction dengan lock untuk mencegah double completion
        try {
            DB::transaction(function () use ($job) {
                // Reload job dengan lock untuk mencegah concurrent updates
                $jobLocked = Job::lockForUpdate()->find($job->job_id);
                
                if (!$jobLocked) {
                    throw new \Exception('Job tidak ditemukan.');
                }

                // Double check status setelah lock (penting untuk race condition)
                if ($jobLocked->status !== Job::STATUS_PROGRESS) {
                    throw new \Exception('Job ini belum dalam status pengerjaan atau sudah diselesaikan.');
                }

                // Pastikan job memiliki assignee
                if (!$jobLocked->assigned_to) {
                    throw new \Exception('Job ini tidak memiliki worker.');
                }

                // Update status job menjadi selesai
                $jobLocked->update([
                    'status' => Job::STATUS_DONE,
                ]);

                // Transfer saldo ke worker yang mengerjakan job
                $worker = $jobLocked->assignee;
                if ($worker) {
                    $worker->increment('balance', $jobLocked->price);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors(['job' => $e->getMessage()]);
        }

        return back()->with('status', 'Job ditandai selesai. Pembayaran telah ditransfer ke worker.');
    }
}

