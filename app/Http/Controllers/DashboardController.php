<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Report;
use App\Models\Topup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk dashboard
 * 
 * Dibuat oleh: Reffael (Role: Admin) - bagian admin dashboard
 * Fitur:
 * - Dashboard admin dengan statistik lengkap
 * 
 * BEST PRACTICE:
 * - Separasi statistik untuk admin dan user biasa
 * - Menggunakan Eloquent query untuk performa yang lebih baik
 * - Null coalescing operator (??) untuk default value
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard dengan statistik sesuai role user
     * 
     * BEST PRACTICE:
     * - Separasi statistik untuk admin dan user biasa
     * - Menggunakan Eloquent query untuk performa yang lebih baik
     * - Null coalescing operator (??) untuk default value
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // BEST PRACTICE: Separasi statistik untuk admin dan user biasa
        if ($user->role === 'admin') {
            // Statistik untuk admin dashboard
            // Dibuat oleh: Reffael (Role: Admin)
            $stats = [
                // Statistik jobs
                'total_jobs' => Job::count(),
                'pending_jobs' => Job::where('status', Job::STATUS_PENDING)->count(),
                'active_jobs' => Job::where('status', Job::STATUS_PROGRESS)->count(),
                'completed_jobs' => Job::where('status', Job::STATUS_DONE)->count(),
                
                // Statistik users (tidak termasuk admin)
                'total_users' => User::where('role', '!=', 'admin')->count(),
                
                // Statistik topup yang pending (perlu approval)
                'pending_topups' => Topup::where('status', Topup::STATUS_PENDING)->count(),
                
                // Statistik reports yang pending (perlu ditinjau)
                'pending_reports' => Report::where('status', Report::STATUS_PENDING)->count(),
                
                // Total revenue dari semua job yang sudah selesai
                // BEST PRACTICE: Menggunakan null coalescing operator (??) untuk default value
                'total_revenue' => Job::where('status', Job::STATUS_DONE)->sum('price') ?? 0,
            ];
        } else {
            // Statistik untuk user biasa (mahasiswa/dosen)
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

