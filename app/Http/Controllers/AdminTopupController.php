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
            ->paginate(10);

        return view('admin.topups.index', [
            'pendingTopups' => $pendingTopups,
            'allTopups' => $allTopups,
        ]);
    }

    public function approve(Request $request, Topup $topup)
    {
        // Gunakan DB transaction dengan lock untuk mencegah double approval
        try {
            DB::transaction(function () use ($topup, $request) {
                // Reload topup dengan lock untuk mencegah concurrent updates
                $topupLocked = Topup::lockForUpdate()->find($topup->topup_id);
                
                if (!$topupLocked) {
                    throw new \Exception('Topup tidak ditemukan.');
                }

                // Double check status setelah lock (penting untuk race condition)
                if ($topupLocked->status !== Topup::STATUS_PENDING) {
                    throw new \Exception('Topup ini sudah diproses.');
                }

                $statusBefore = $topupLocked->status;
                $topupLocked->update(['status' => Topup::STATUS_APPROVED]);

                $user = $topupLocked->user;
                $user->increment('balance', $topupLocked->amount);

                TopupHistory::create([
                    'topup_id' => $topupLocked->topup_id,
                    'approved_by' => $request->user()->user_id,
                    'status_before' => $statusBefore,
                    'status_after' => Topup::STATUS_APPROVED,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['topup' => $e->getMessage()]);
        }

        return back()->with('status', 'Topup berhasil disetujui. Saldo user telah ditambahkan.');
    }

    public function reject(Request $request, Topup $topup)
    {
        // Gunakan DB transaction dengan lock untuk mencegah double rejection
        try {
            DB::transaction(function () use ($topup, $request) {
                // Reload topup dengan lock untuk mencegah concurrent updates
                $topupLocked = Topup::lockForUpdate()->find($topup->topup_id);
                
                if (!$topupLocked) {
                    throw new \Exception('Topup tidak ditemukan.');
                }

                // Double check status setelah lock (penting untuk race condition)
                if ($topupLocked->status !== Topup::STATUS_PENDING) {
                    throw new \Exception('Topup ini sudah diproses.');
                }

                $statusBefore = $topupLocked->status;
                $topupLocked->update(['status' => Topup::STATUS_REJECTED]);

                TopupHistory::create([
                    'topup_id' => $topupLocked->topup_id,
                    'approved_by' => $request->user()->user_id,
                    'status_before' => $statusBefore,
                    'status_after' => Topup::STATUS_REJECTED,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['topup' => $e->getMessage()]);
        }

        return back()->with('status', 'Topup ditolak.');
    }
}

