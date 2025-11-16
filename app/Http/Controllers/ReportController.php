<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        $user = auth()->user();

        // Get job_id jika ada (untuk report terkait job)
        $jobId = $request->query('job_id');
        $reportedUserId = $request->query('user_id');

        $job = null;
        $reportedUser = null;

        if ($jobId) {
            $job = Job::with(['creator', 'assignee'])->findOrFail($jobId);
            
            // Tentukan user yang dilaporkan berdasarkan role
            if ($user->user_id === $job->created_by) {
                // Creator melaporkan worker
                $reportedUser = $job->assignee;
            } elseif ($user->user_id === $job->assigned_to) {
                // Worker melaporkan creator
                $reportedUser = $job->creator;
            }
        } elseif ($reportedUserId) {
            $reportedUser = User::findOrFail($reportedUserId);
        }

        return view('reports.create', [
            'job' => $job,
            'reportedUser' => $reportedUser,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'job_id' => ['nullable', 'exists:jobs,job_id'],
            'reported_user_id' => ['nullable', 'exists:users,user_id'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        // Validasi: harus ada job_id atau reported_user_id
        if (!$data['job_id'] && !$data['reported_user_id']) {
            return back()->withErrors(['description' => 'Harus memilih job atau user yang dilaporkan.'])->withInput();
        }

        // Jika ada job_id, validasi user yang dilaporkan
        if ($data['job_id']) {
            $job = Job::findOrFail($data['job_id']);
            
            // Tentukan user yang dilaporkan
            if ($user->user_id === $job->created_by) {
                // Creator melaporkan worker
                $reportedUserId = $job->assigned_to;
            } elseif ($user->user_id === $job->assigned_to) {
                // Worker melaporkan creator
                $reportedUserId = $job->created_by;
            } else {
                return back()->withErrors(['description' => 'Anda tidak terlibat dalam job ini.'])->withInput();
            }

            if (!$reportedUserId) {
                return back()->withErrors(['description' => 'Job ini tidak memiliki user yang bisa dilaporkan.'])->withInput();
            }
        } else {
            $reportedUserId = $data['reported_user_id'];
        }

        // Validasi: tidak bisa report diri sendiri
        if ($reportedUserId === $user->user_id) {
            return back()->withErrors(['description' => 'Anda tidak bisa melaporkan diri sendiri.'])->withInput();
        }

        Report::create([
            'reported_by' => $user->user_id,
            'reported_user' => $reportedUserId,
            'description' => $data['description'],
            'status' => Report::STATUS_PENDING,
        ]);

        return redirect()->route('jobs.index')->with('status', 'Laporan berhasil dikirim. Admin akan meninjau laporan Anda.');
    }
}
