<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function create(Job $job)
    {
        $user = auth()->user();

        // Validasi: hanya creator yang bisa rating, dan job harus sudah selesai
        if ($job->created_by !== $user->user_id) {
            abort(403, 'Hanya creator job yang bisa memberikan rating.');
        }

        if ($job->status !== Job::STATUS_DONE) {
            abort(403, 'Hanya bisa rating job yang sudah selesai.');
        }

        // Cek apakah sudah ada feedback
        $existingFeedback = Feedback::where('job_id', $job->job_id)->first();
        if ($existingFeedback) {
            return redirect()->route('jobs.index')->withErrors(['feedback' => 'Anda sudah memberikan rating untuk job ini.']);
        }

        return view('feedback.create', [
            'job' => $job->load(['assignee', 'categories']),
        ]);
    }

    public function store(Request $request, Job $job)
    {
        $user = auth()->user();

        // Validasi
        if ($job->created_by !== $user->user_id) {
            abort(403, 'Hanya creator job yang bisa memberikan rating.');
        }

        if ($job->status !== Job::STATUS_DONE) {
            abort(403, 'Hanya bisa rating job yang sudah selesai.');
        }

        // Cek apakah sudah ada feedback
        $existingFeedback = Feedback::where('job_id', $job->job_id)->first();
        if ($existingFeedback) {
            return back()->withErrors(['feedback' => 'Anda sudah memberikan rating untuk job ini.']);
        }

        if (!$job->assigned_to) {
            return back()->withErrors(['feedback' => 'Job ini tidak memiliki worker yang bisa di-rating.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data, $job, $user) {
            // Buat feedback
            Feedback::create([
                'job_id' => $job->job_id,
                'given_by' => $user->user_id,
                'given_to' => $job->assigned_to,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            // Update avg_rating worker
            $worker = $job->assignee;
            if ($worker) {
                $worker->updateAvgRating();
            }
        });

        return redirect()->route('jobs.index')->with('status', 'Rating berhasil diberikan. Terima kasih!');
    }
}
