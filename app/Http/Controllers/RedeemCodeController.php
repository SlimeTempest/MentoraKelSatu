<?php

namespace App\Http\Controllers;

use App\Models\RedeemCode;
use Illuminate\Http\Request;
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

        do {
            $code = strtoupper(Str::random(8));
        } while (RedeemCode::where('code', $code)->exists());

        RedeemCode::create([
            'code' => $code,
            'created_by' => auth()->user()->user_id,
            'amount' => $data['amount'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return redirect()->route('redeem-codes.index')->with('status', 'Redeem code berhasil dibuat!');
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
        $redeemCode = RedeemCode::where('code', $code)->first();

        if (!$redeemCode) {
            return back()->withErrors(['code' => 'Redeem code tidak ditemukan.'])->withInput();
        }

        if ($redeemCode->is_claimed) {
            return back()->withErrors(['code' => 'Redeem code sudah digunakan.'])->withInput();
        }

        if ($redeemCode->isExpired()) {
            return back()->withErrors(['code' => 'Redeem code sudah kadaluarsa.'])->withInput();
        }

        $user = auth()->user();
        $redeemCode->update([
            'is_claimed' => true,
            'claimed_by' => $user->user_id,
            'claimed_at' => now(),
        ]);

        $user->increment('balance', $redeemCode->amount);

        return redirect()->route('redeem-codes.claim')->with('status', 'Redeem code berhasil diklaim! Saldo Anda bertambah Rp ' . number_format($redeemCode->amount, 0, ',', '.'));
    }
}
