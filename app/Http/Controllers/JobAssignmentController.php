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

        if ($job->status !== Job::STATUS_PENDING) {
            return back()->withErrors(['job' => 'Job sudah tidak tersedia.']);
        }

        if ($job->created_by === $user->user_id) {
            return back()->withErrors(['job' => 'Kamu tidak bisa mengambil job buatan sendiri.']);
        }

        $activeAssignments = $user->jobsAssigned()
            ->where('status', Job::STATUS_PROGRESS)
            ->count();

        if ($activeAssignments >= 2) {
            return back()->withErrors(['job' => 'Kamu hanya bisa mengerjakan maksimal 2 job sekaligus.']);
        }

        $job->update([
            'assigned_to' => $user->user_id,
            'status' => Job::STATUS_PROGRESS,
        ]);

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

        DB::transaction(function () use ($job) {
            // Update status job menjadi selesai
            $job->update([
                'status' => Job::STATUS_DONE,
            ]);

            // Transfer saldo ke worker yang mengerjakan job
            if ($job->assigned_to) {
                $worker = $job->assignee;
                if ($worker) {
                    $worker->increment('balance', $job->price);
                }
            }
        });

        return back()->with('status', 'Job ditandai selesai. Pembayaran telah ditransfer ke worker.');
    }
}

