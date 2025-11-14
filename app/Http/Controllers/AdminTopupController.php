<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\TopupHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTopupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }
        $pendingTopups = Topup::with(['user'])
            ->where('status', Topup::STATUS_PENDING)
            ->latest()
            ->get();

        $allTopups = Topup::with(['user'])
            ->latest()
            ->paginate(20);

        return view('admin.topups.index', [
            'pendingTopups' => $pendingTopups,
            'allTopups' => $allTopups,
        ]);
    }

    public function approve(Request $request, Topup $topup)
    {
        if ($topup->status !== Topup::STATUS_PENDING) {
            return back()->withErrors(['topup' => 'Topup ini sudah diproses.']);
        }

        DB::transaction(function () use ($topup, $request) {
            $statusBefore = $topup->status;
            $topup->update(['status' => Topup::STATUS_APPROVED]);

            $user = $topup->user;
            $user->increment('balance', $topup->amount);

            TopupHistory::create([
                'topup_id' => $topup->topup_id,
                'approved_by' => $request->user()->user_id,
                'status_before' => $statusBefore,
                'status_after' => Topup::STATUS_APPROVED,
            ]);
        });

        return back()->with('status', 'Topup berhasil disetujui. Saldo user telah ditambahkan.');
    }

    public function reject(Request $request, Topup $topup)
    {
        if ($topup->status !== Topup::STATUS_PENDING) {
            return back()->withErrors(['topup' => 'Topup ini sudah diproses.']);
        }

        DB::transaction(function () use ($topup, $request) {
            $statusBefore = $topup->status;
            $topup->update(['status' => Topup::STATUS_REJECTED]);

            TopupHistory::create([
                'topup_id' => $topup->topup_id,
                'approved_by' => $request->user()->user_id,
                'status_before' => $statusBefore,
                'status_after' => Topup::STATUS_REJECTED,
            ]);
        });

        return back()->with('status', 'Topup ditolak.');
    }
}

