<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk manajemen laporan (reports) oleh admin
 * 
 * Dibuat oleh: Reffael (Role: Admin)
 * Fitur:
 * - Manajemen laporan dari user
 * - Update status laporan (pending, on_review, done)
 * 
 * BEST PRACTICE:
 * - Eager loading relationship untuk menghindari N+1 query problem
 * - Validasi authorization di setiap method
 * - Separasi pending reports dan all reports untuk UX yang lebih baik
 */
class AdminReportController extends Controller
{
    /**
     * Menampilkan daftar semua laporan dan laporan yang pending
     * 
     * BEST PRACTICE:
     * - Eager loading 'reporter' dan 'reportedUser' untuk menghindari N+1 query problem
     * - Separasi pending reports dan all reports untuk UX yang lebih baik
     * - Pagination untuk performa yang lebih baik pada data besar
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Eager loading relationship untuk menghindari N+1 query problem
        // Mengambil laporan yang masih pending untuk ditampilkan di bagian atas
        $pendingReports = Report::with(['reporter', 'reportedUser'])
            ->where('status', Report::STATUS_PENDING)
            ->latest()
            ->get();

        // BEST PRACTICE: Pagination untuk performa yang lebih baik pada data besar
        // Mengambil semua laporan dengan pagination untuk ditampilkan di tabel
        $allReports = Report::with(['reporter', 'reportedUser'])
            ->latest()
            ->paginate(10);

        return view('admin.reports.index', [
            'pendingReports' => $pendingReports,
            'allReports' => $allReports,
        ]);
    }

    /**
     * Menampilkan detail laporan
     * 
     * BEST PRACTICE:
     * - Eager loading relationship untuk menghindari N+1 query problem
     * - Menggunakan firstOrFail() untuk error handling yang lebih baik
     * 
     * @param Request $request
     * @param string $report Report ID
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $report)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Menggunakan firstOrFail() untuk error handling yang lebih baik
        // Jika report tidak ditemukan, akan otomatis return 404
        $report = Report::where('report_id', $report)->firstOrFail();
        
        // BEST PRACTICE: Eager loading relationship untuk menghindari N+1 query problem
        $report->load(['reporter', 'reportedUser']);

        return view('admin.reports.show', [
            'report' => $report,
        ]);
    }

    /**
     * Mengupdate status laporan
     * 
     * BEST PRACTICE:
     * - Validasi input menggunakan Laravel validation dengan 'in' rule
     * - Menggunakan firstOrFail() untuk error handling yang lebih baik
     * - Memberikan feedback yang jelas kepada admin
     * 
     * Status yang valid:
     * - pending: Laporan baru, belum ditinjau
     * - on_review: Laporan sedang ditinjau oleh admin
     * - done: Laporan sudah selesai ditangani
     * 
     * @param Request $request
     * @param string $report Report ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $report)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Menggunakan firstOrFail() untuk error handling yang lebih baik
        $report = Report::where('report_id', $report)->firstOrFail();

        // BEST PRACTICE: Validasi input menggunakan Laravel validation dengan 'in' rule
        // 'in' rule memastikan hanya status yang valid yang bisa di-set
        $data = $request->validate([
            'status' => ['required', 'in:pending,on_review,done'],
        ]);

        // Update status laporan
        $report->update(['status' => $data['status']]);

        return back()->with('status', 'Status laporan berhasil diperbarui.');
    }
}
