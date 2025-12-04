<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Report;
use App\Models\Topup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            $stats = [
                'total_jobs' => Job::count(),
                'pending_jobs' => Job::where('status', Job::STATUS_PENDING)->count(),
                'active_jobs' => Job::where('status', Job::STATUS_PROGRESS)->count(),
                'completed_jobs' => Job::where('status', Job::STATUS_DONE)->count(),
                'total_users' => User::where('role', '!=', 'admin')->count(),
                'pending_topups' => Topup::where('status', Topup::STATUS_PENDING)->count(),
                'pending_reports' => Report::where('status', Report::STATUS_PENDING)->count(),
                'total_revenue' => Job::where('status', Job::STATUS_DONE)->sum('price') ?? 0,
            ];
        } else {
            $stats = [
                'jobs_created' => $user->jobsCreated()->count(),
                'jobs_taken' => $user->jobsAssigned()->count(),
                'jobs_in_progress' => $user->jobsAssigned()->where('status', Job::STATUS_PROGRESS)->count(),
                'jobs_completed' => $user->jobsAssigned()->where('status', Job::STATUS_DONE)->count(),
                'total_earned' => $user->jobsAssigned()->where('status', Job::STATUS_DONE)->sum('price') ?? 0,
                'available_jobs' => Job::available()->count(),
                'balance' => $user->balance ?? 0,
            ];
        }

        return view('dashboard', compact('stats'));
    }
}

