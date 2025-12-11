<?php

namespace App\Http\Requests;

use App\Models\Feedback;
use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Form Request untuk validasi feedback
 * 
 * BEST PRACTICES:
 * - Memisahkan validasi dari controller (separation of concerns)
 * - Custom validation rules untuk business logic
 * - Custom error messages yang user-friendly
 */
class StoreFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * BEST PRACTICE: Authorization di Form Request level
     */
    public function authorize(): bool
    {
        $job = $this->route('job');
        $user = $this->user();

        // BEST PRACTICE: Null check untuk keamanan
        if (!$job || !$user) {
            return false;
        }

        // Hanya creator job yang bisa memberikan feedback
        if ($job->created_by !== $user->user_id) {
            return false;
        }

        // Hanya job yang sudah selesai yang bisa di-rating
        if ($job->status !== Job::STATUS_DONE) {
            return false;
        }

        // Cek apakah sudah ada feedback (prevent duplicate)
        $existingFeedback = Feedback::where('job_id', $job->job_id)->first();
        if ($existingFeedback) {
            return false;
        }

        // Job harus punya assignee
        if (!$job->assigned_to) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * BEST PRACTICE: Validasi input dengan rules yang jelas
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * 
     * BEST PRACTICE: Pesan error yang jelas dan user-friendly
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Rating wajib diisi.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal adalah 1.',
            'rating.max' => 'Rating maksimal adalah 5.',
            'comment.string' => 'Komentar harus berupa teks.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ];
    }

    /**
     * Configure the validator instance.
     * 
     * BEST PRACTICE: Custom validation logic jika diperlukan
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Trim whitespace dari comment jika ada
            if ($this->has('comment') && $this->comment !== null) {
                $this->merge([
                    'comment' => trim($this->comment)
                ]);
            }
        });
    }

    /**
     * Handle a failed authorization attempt.
     * 
     * BEST PRACTICE: Custom error response untuk authorization failure
     */
    protected function failedAuthorization(): void
    {
        $job = $this->route('job');
        $user = $this->user();

        // BEST PRACTICE: Null check dan pesan error yang spesifik
        if (!$job || !$user) {
            abort(403, 'Data tidak valid.');
        }

        if ($job->created_by !== $user->user_id) {
            abort(403, 'Hanya creator job yang bisa memberikan rating.');
        }

        if ($job->status !== Job::STATUS_DONE) {
            abort(403, 'Hanya bisa rating job yang sudah selesai.');
        }

        $existingFeedback = Feedback::where('job_id', $job->job_id)->first();
        if ($existingFeedback) {
            abort(403, 'Anda sudah memberikan rating untuk job ini.');
        }

        if (!$job->assigned_to) {
            abort(403, 'Job ini tidak memiliki worker yang bisa di-rating.');
        }

        abort(403, 'Anda tidak memiliki izin untuk melakukan aksi ini.');
    }
}

