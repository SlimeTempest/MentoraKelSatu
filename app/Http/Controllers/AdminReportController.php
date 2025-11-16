<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $pendingReports = Report::with(['reporter', 'reportedUser'])
            ->where('status', Report::STATUS_PENDING)
            ->latest()
            ->get();

        $allReports = Report::with(['reporter', 'reportedUser'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', [
            'pendingReports' => $pendingReports,
            'allReports' => $allReports,
        ]);
    }

    public function show(Request $request, $report)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $report = Report::where('report_id', $report)->firstOrFail();
        $report->load(['reporter', 'reportedUser']);

        return view('admin.reports.show', [
            'report' => $report,
        ]);
    }

    public function updateStatus(Request $request, $report)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $report = Report::where('report_id', $report)->firstOrFail();

        $data = $request->validate([
            'status' => ['required', 'in:pending,on_review,done'],
        ]);

        $report->update(['status' => $data['status']]);

        return back()->with('status', 'Status laporan berhasil diperbarui.');
    }
}
