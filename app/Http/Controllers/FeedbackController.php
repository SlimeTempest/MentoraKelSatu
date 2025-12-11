<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\Job;
use Illuminate\Support\Facades\DB;

/**
 * FeedbackController
 * 
 * BEST PRACTICES:
 * 1. Authorization: Hanya creator job yang sudah selesai yang bisa memberikan feedback
 * 2. Validation: Pastikan job sudah DONE dan belum ada feedback sebelumnya
 * 3. Transaction: Gunakan DB transaction untuk menjaga konsistensi data
 * 4. Rating Update: Update avg_rating worker setelah feedback dibuat
 * 5. Error Handling: Berikan pesan error yang jelas dan user-friendly
 * 6. Single Responsibility: Satu job hanya bisa dapat satu feedback (one-to-one relationship)
 */
class FeedbackController extends Controller
{
    /**
     * Menampilkan form untuk memberikan feedback/rating
     * 
     * BEST PRACTICES:
     * - Validasi authorization sebelum menampilkan form
     * - Cek status job (harus DONE)
     * - Cek apakah sudah ada feedback (prevent duplicate)
     * - Eager load relationships untuk menghindari N+1 query
     * 
     * @param Job $job
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Job $job)
    {
        // BEST PRACTICE: Extract validasi ke method terpisah untuk reusability
        $validationResult = $this->validateFeedbackAccess($job);
        if ($validationResult !== true) {
            return $validationResult;
        }

        // BEST PRACTICE: Eager load relationships untuk menghindari N+1 query
        return view('feedback.create', [
            'job' => $job->load(['assignee', 'categories']),
        ]);
    }

    /**
     * Menyimpan feedback/rating dari user
     * 
     * BEST PRACTICES:
     * - Gunakan Form Request untuk validasi (separation of concerns)
     * - Validasi authorization sudah di-handle oleh Form Request
     * - Gunakan DB transaction untuk atomicity (semua operasi sukses atau rollback)
     * - Update avg_rating setelah feedback dibuat untuk menjaga data konsisten
     * - Berikan feedback yang jelas ke user (success/error messages)
     * 
     * @param StoreFeedbackRequest $request
     * @param Job $job
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreFeedbackRequest $request, Job $job)
    {
        $user = $request->user();
        $data = $request->validated();

        // BEST PRACTICE: Gunakan DB transaction untuk atomicity
        // Jika create feedback gagal atau update rating gagal, semua di-rollback
        DB::transaction(function () use ($data, $job, $user) {
            // Buat feedback record
            Feedback::create([
                'job_id' => $job->job_id,
                'given_by' => $user->user_id,
                'given_to' => $job->assigned_to,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            // BEST PRACTICE: Update avg_rating worker setelah feedback dibuat
            // Ini menjaga konsistensi data rating di user table
            // Reload assignee untuk memastikan data terbaru
            $job->load('assignee');
            $worker = $job->assignee;
            if ($worker) {
                $worker->updateAvgRating();
            }
        });

        return redirect()
            ->route('jobs.index')
            ->with('status', 'Rating berhasil diberikan. Terima kasih!');
    }

    /**
     * Validasi akses untuk memberikan feedback
     * 
     * BEST PRACTICE: Extract method untuk menghindari duplikasi kode
     * Method ini digunakan di create() untuk validasi sebelum menampilkan form
     * 
     * @param Job $job
     * @return true|\Illuminate\Http\RedirectResponse
     */
    protected function validateFeedbackAccess(Job $job)
    {
        $user = auth()->user();

        // Validasi: hanya creator yang bisa rating
        if ($job->created_by !== $user->user_id) {
            return redirect()
                ->route('jobs.index')
                ->withErrors(['feedback' => 'Hanya creator job yang bisa memberikan rating.']);
        }

        // Validasi: job harus sudah selesai
        if ($job->status !== Job::STATUS_DONE) {
            return redirect()
                ->route('jobs.index')
                ->withErrors(['feedback' => 'Hanya bisa rating job yang sudah selesai.']);
        }

        // Cek apakah sudah ada feedback (prevent duplicate)
        $existingFeedback = Feedback::where('job_id', $job->job_id)->first();
        if ($existingFeedback) {
            return redirect()
                ->route('jobs.index')
                ->withErrors(['feedback' => 'Anda sudah memberikan rating untuk job ini.']);
        }

        // Cek apakah job memiliki assignee
        if (!$job->assigned_to) {
            return redirect()
                ->route('jobs.index')
                ->withErrors(['feedback' => 'Job ini tidak memiliki worker yang bisa di-rating.']);
        }

        return true;
    }
}
