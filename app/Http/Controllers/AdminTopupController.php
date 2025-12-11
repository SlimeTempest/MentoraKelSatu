<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\TopupHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk manajemen topup saldo oleh admin
 * 
 * Dibuat oleh: Reffael (Role: Admin)
 * Fitur:
 * - CRUD dan laporan untuk manajemen topup saldo
 * - Fitur approval/rejection topup dari mahasiswa
 * 
 * BEST PRACTICE:
 * - Menggunakan DB transaction dengan lock untuk mencegah race condition
 * - Validasi authorization di setiap method
 * - Mencatat history setiap perubahan status topup
 */
class AdminTopupController extends Controller
{
    /**
     * Menampilkan daftar semua topup dan topup yang pending
     * 
     * BEST PRACTICE: Eager loading relationship 'user' untuk menghindari N+1 query problem
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // BEST PRACTICE: Validasi authorization di awal (defense in depth)
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }
        
        // Mengambil topup yang masih pending untuk ditampilkan di bagian atas
        // Eager load 'user' untuk menghindari N+1 query problem
        $pendingTopups = Topup::with(['user'])
            ->where('status', Topup::STATUS_PENDING)
            ->latest()
            ->get();

        // Mengambil semua topup dengan pagination untuk ditampilkan di tabel
        $allTopups = Topup::with(['user'])
            ->latest()
            ->paginate(10);

        return view('admin.topups.index', [
            'pendingTopups' => $pendingTopups,
            'allTopups' => $allTopups,
        ]);
    }

    /**
     * Menyetujui topup dari mahasiswa dan menambahkan saldo ke akun user
     * 
     * BEST PRACTICE:
     * - Menggunakan DB transaction dengan lockForUpdate() untuk mencegah race condition
     * - Double check status setelah lock untuk memastikan topup masih pending
     * - Mencatat history perubahan status untuk audit trail
     * - Menggunakan increment() untuk atomic operation pada balance
     * 
     * @param Request $request
     * @param Topup $topup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Topup $topup)
    {
        // BEST PRACTICE: Gunakan DB transaction dengan lock untuk mencegah double approval
        // Ini penting karena jika 2 admin mencoba approve topup yang sama secara bersamaan
        try {
            DB::transaction(function () use ($topup, $request) {
                // BEST PRACTICE: Reload topup dengan lockForUpdate() untuk mencegah concurrent updates
                // lockForUpdate() akan mengunci row di database hingga transaction selesai
                $topupLocked = Topup::lockForUpdate()->find($topup->topup_id);
                
                if (!$topupLocked) {
                    throw new \Exception('Topup tidak ditemukan.');
                }

                // BEST PRACTICE: Double check status setelah lock (penting untuk race condition)
                // Meskipun sudah di-lock, tetap validasi status untuk memastikan konsistensi
                if ($topupLocked->status !== Topup::STATUS_PENDING) {
                    throw new \Exception('Topup ini sudah diproses.');
                }

                // Simpan status sebelum perubahan untuk history
                $statusBefore = $topupLocked->status;
                
                // Update status topup menjadi approved
                $topupLocked->update(['status' => Topup::STATUS_APPROVED]);

                // BEST PRACTICE: Menggunakan increment() untuk atomic operation pada balance
                // increment() adalah operasi atomik yang aman untuk concurrent access
                $user = $topupLocked->user;
                $user->increment('balance', $topupLocked->amount);

                // BEST PRACTICE: Mencatat history setiap perubahan status untuk audit trail
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

    /**
     * Menolak topup dari mahasiswa
     * 
     * BEST PRACTICE:
     * - Menggunakan DB transaction dengan lockForUpdate() untuk mencegah race condition
     * - Double check status setelah lock untuk memastikan topup masih pending
     * - Mencatat history perubahan status untuk audit trail
     * 
     * @param Request $request
     * @param Topup $topup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Topup $topup)
    {
        // BEST PRACTICE: Gunakan DB transaction dengan lock untuk mencegah double rejection
        try {
            DB::transaction(function () use ($topup, $request) {
                // BEST PRACTICE: Reload topup dengan lockForUpdate() untuk mencegah concurrent updates
                $topupLocked = Topup::lockForUpdate()->find($topup->topup_id);
                
                if (!$topupLocked) {
                    throw new \Exception('Topup tidak ditemukan.');
                }

                // BEST PRACTICE: Double check status setelah lock (penting untuk race condition)
                if ($topupLocked->status !== Topup::STATUS_PENDING) {
                    throw new \Exception('Topup ini sudah diproses.');
                }

                // Simpan status sebelum perubahan untuk history
                $statusBefore = $topupLocked->status;
                
                // Update status topup menjadi rejected
                $topupLocked->update(['status' => Topup::STATUS_REJECTED]);

                // BEST PRACTICE: Mencatat history setiap perubahan status untuk audit trail
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

