<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $allJobs = Job::with(['creator', 'assignee', 'categories'])
                ->latest()
                ->get();

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
            'availableJobs' => $availableJobsQuery->get(),
            'myJobs' => $user->jobsCreated()->with(['assignee', 'categories'])->latest()->get(),
            'assignedJobs' => $user->jobsAssigned()->with(['creator', 'categories'])->latest()->get(),
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

        DB::transaction(function () use ($request, $data) {
            $job = Job::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'deadline' => $data['deadline'] ?? null,
                'price' => $data['price'],
                'created_by' => $request->user()->user_id,
                'status' => Job::STATUS_PENDING,
            ]);

            $job->categories()->sync($data['categories'] ?? []);
        });

        return redirect()->route('jobs.index')->with('status', 'Job berhasil dibuat.');
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

        DB::transaction(function () use ($job, $data) {
            $job->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'deadline' => $data['deadline'] ?? null,
                'price' => $data['price'],
            ]);

            $job->categories()->sync($data['categories'] ?? []);
        });

        return redirect()->route('jobs.index')->with('status', 'Job berhasil diperbarui.');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return redirect()->route('jobs.index')->with('status', 'Job berhasil dihapus.');
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

