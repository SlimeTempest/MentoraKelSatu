<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Services\JobExpirationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index(Request $request, JobExpirationService $expirationService)
    {
        // Auto-check dan expire jobs yang sudah melewati deadline
        // Ini akan otomatis berjalan setiap kali ada yang akses halaman jobs
        $expirationService->expireJobs();

        $user = $request->user();

        if ($user->role === 'admin') {
            $allJobs = Job::with(['creator', 'assignee', 'categories', 'feedback'])
                ->latest()
                ->paginate(10);

            return view('jobs.index', [
                'allJobs' => $allJobs,
                'isAdmin' => true,
            ]);
        }

        $availableJobsQuery = Job::with(['creator', 'categories'])
            ->available()
            ->where('created_by', '!=', $user->user_id)
            ->orderByDesc('created_at');

        return view('jobs.index', [
            'availableJobs' => $availableJobsQuery->paginate(10),
            'myJobs' => $user->jobsCreated()->with(['assignee', 'categories', 'feedback'])->latest()->paginate(10),
            'assignedJobs' => $user->jobsAssigned()->with(['creator', 'categories'])->latest()->paginate(10),
            'isAdmin' => false,
        ]);
    }

    public function create(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('jobs.index')->withErrors(['job' => 'Admin tidak bisa membuat job.']);
        }

        $categories = Category::orderBy('name')->get();

        return view('jobs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('jobs.index')->withErrors(['job' => 'Admin tidak bisa membuat job.']);
        }

        $data = $this->validateJob($request);
        $user = $request->user();

        // Validasi saldo cukup
        if ($user->balance < $data['price']) {
            return back()->withErrors([
                'price' => 'Saldo Anda tidak cukup. Saldo saat ini: Rp ' . number_format($user->balance, 0, ',', '.') . '. Topup saldo terlebih dahulu.',
            ])->withInput();
        }

        DB::transaction(function () use ($request, $data, $user) {
            $job = Job::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'deadline' => $data['deadline'] ?? null,
                'price' => $data['price'],
                'created_by' => $user->user_id,
                'status' => Job::STATUS_PENDING,
            ]);

            $job->categories()->sync($data['categories'] ?? []);

            // Potong saldo user
            $user->decrement('balance', $data['price']);
        });

        return redirect()->route('jobs.index')->with('status', 'Job berhasil dibuat. Saldo Anda telah dipotong.');
    }

    public function edit(Job $job)
    {
        $this->authorize('update', $job);

        $categories = Category::orderBy('name')->get();

        return view('jobs.edit', [
            'job' => $job->load('categories'),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $data = $this->validateJob($request);
        $user = $request->user();

        // Validasi: Jika job sudah diambil (PROGRESS), tidak boleh ubah harga
        // Karena worker sudah setuju dengan harga tertentu
        if ($job->status === Job::STATUS_PROGRESS && $data['price'] != $job->price) {
            return back()->withErrors([
                'price' => 'Tidak bisa mengubah harga job yang sedang dikerjakan. Worker sudah setuju dengan harga yang ada.',
            ])->withInput();
        }

        // Validasi: Jika job sudah selesai atau expired, tidak bisa diubah
        if (in_array($job->status, [Job::STATUS_DONE, Job::STATUS_EXPIRED])) {
            return back()->withErrors([
                'job' => 'Job yang sudah selesai atau kadaluarsa tidak bisa diubah.',
            ])->withInput();
        }

        // Jika harga naik, cek saldo cukup untuk selisihnya
        if ($data['price'] > $job->price) {
            $selisih = $data['price'] - $job->price;
            if ($user->balance < $selisih) {
                return back()->withErrors([
                    'price' => 'Saldo Anda tidak cukup untuk menaikkan harga. Saldo saat ini: Rp ' . number_format($user->balance, 0, ',', '.') . '. Butuh tambahan: Rp ' . number_format($selisih, 0, ',', '.') . '.',
                ])->withInput();
            }
        }

        DB::transaction(function () use ($job, $data, $user) {
            $hargaLama = $job->price;
            
            $job->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'deadline' => $data['deadline'] ?? null,
                'price' => $data['price'],
            ]);

            $job->categories()->sync($data['categories'] ?? []);

            // Jika harga naik, potong selisih dari saldo
            if ($data['price'] > $hargaLama) {
                $selisih = $data['price'] - $hargaLama;
                $user->decrement('balance', $selisih);
            }
            // Jika harga turun, kembalikan selisih ke saldo
            elseif ($data['price'] < $hargaLama) {
                $selisih = $hargaLama - $data['price'];
                $user->increment('balance', $selisih);
            }
        });

        return redirect()->route('jobs.index')->with('status', 'Job berhasil diperbarui.');
    }

    public function destroy(Request $request, Job $job)
    {
        $this->authorize('delete', $job);

        $user = $request->user();
        $jobStatus = $job->status; // Simpan status sebelum delete untuk pesan

        DB::transaction(function () use ($job, $user) {
            // Validasi: Admin bisa delete kapan saja, tapi untuk non-admin hanya bisa delete jika PENDING
            // Policy sudah handle ini, tapi double check untuk safety
            if ($user->role !== 'admin' && $job->status !== Job::STATUS_PENDING) {
                abort(403, 'Hanya admin yang bisa menghapus job yang sudah diambil atau selesai.');
            }

            // Kembalikan saldo jika job belum selesai (PENDING atau PROGRESS)
            // Jika sudah DONE, saldo sudah terbayar ke worker, jadi tidak perlu dikembalikan
            // Jika sudah EXPIRED, saldo sudah dikembalikan saat expired
            if (in_array($job->status, [Job::STATUS_PENDING, Job::STATUS_PROGRESS]) && $job->creator) {
                $job->creator->increment('balance', $job->price);
            }

            $job->delete();
        });

        $message = in_array($jobStatus, [Job::STATUS_PENDING, Job::STATUS_PROGRESS])
            ? 'Job berhasil dihapus. Saldo telah dikembalikan.'
            : 'Job berhasil dihapus.';

        return redirect()->route('jobs.index')->with('status', $message);
    }

    protected function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,category_id'],
        ]);
    }
}

