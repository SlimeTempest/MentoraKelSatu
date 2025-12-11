<?php

namespace App\Http\Controllers;

use App\Models\RedeemCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RedeemCodeController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'dosen') {
            abort(403, 'Hanya dosen yang dapat membuat redeem code.');
        }

        $redeemCodes = auth()->user()
            ->redeemCodesCreated()
            ->with('claimer')
            ->latest()
            ->get();

        return view('redeem-codes.index', compact('redeemCodes'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'dosen') {
            abort(403, 'Hanya dosen yang dapat membuat redeem code.');
        }

        return view('redeem-codes.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'dosen') {
            abort(403, 'Hanya dosen yang dapat membuat redeem code.');
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $user = auth()->user();

        // Validasi saldo dosen cukup
        if ($user->balance < $data['amount']) {
            return back()->withErrors([
                'amount' => 'Saldo Anda tidak cukup. Saldo saat ini: Rp ' . number_format($user->balance, 0, ',', '.') . '. Topup saldo terlebih dahulu.',
            ])->withInput();
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (RedeemCode::where('code', $code)->exists());

        DB::transaction(function () use ($data, $code, $user) {
            // Buat redeem code
            RedeemCode::create([
                'code' => $code,
                'created_by' => $user->user_id,
                'amount' => $data['amount'],
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            // Potong saldo dosen
            $user->decrement('balance', $data['amount']);
        });

        return redirect()->route('redeem-codes.index')->with('status', 'Redeem code berhasil dibuat! Saldo Anda telah dipotong sebesar Rp ' . number_format($data['amount'], 0, ',', '.') . '.');
    }

    public function claim()
    {
        if (auth()->user()->role !== 'mahasiswa') {
            abort(403, 'Hanya mahasiswa yang dapat mengklaim redeem code.');
        }

        return view('redeem-codes.claim');
    }

    public function claimStore(Request $request)
    {
        if (auth()->user()->role !== 'mahasiswa') {
            abort(403, 'Hanya mahasiswa yang dapat mengklaim redeem code.');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'size:8'],
        ]);

        $code = strtoupper($data['code']);
        $user = auth()->user();
        $amount = 0;
        
        // Gunakan DB transaction dengan lock untuk mencegah double claim
        try {
            DB::transaction(function () use ($code, $user, &$amount) {
                // Reload redeem code dengan lock untuk mencegah concurrent claims
                $redeemCode = RedeemCode::lockForUpdate()->where('code', $code)->first();
                
                if (!$redeemCode) {
                    throw new \Exception('Redeem code tidak ditemukan.');
                }

                // Double check setelah lock (penting untuk race condition)
                if ($redeemCode->is_claimed) {
                    throw new \Exception('Redeem code sudah digunakan.');
                }

                if ($redeemCode->isExpired()) {
                    throw new \Exception('Redeem code sudah kadaluarsa.');
                }

                // Simpan amount sebelum update
                $amount = $redeemCode->amount;

                // Update redeem code sebagai claimed
                $redeemCode->update([
                    'is_claimed' => true,
                    'claimed_by' => $user->user_id,
                    'claimed_at' => now(),
                ]);

                // Tambahkan saldo ke mahasiswa
                $user->increment('balance', $amount);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()])->withInput();
        }

        return redirect()->route('redeem-codes.claim')->with('status', 'Redeem code berhasil diklaim! Saldo Anda bertambah Rp ' . number_format($amount, 0, ',', '.'));
    }
}
