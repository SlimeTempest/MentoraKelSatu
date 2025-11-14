<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TopupController extends Controller
{
    public function index(Request $request)
    {
        $topups = $request->user()
            ->topups()
            ->latest()
            ->get();

        return view('topups.index', compact('topups'));
    }

    public function create()
    {
        $topupConfig = config('topup');
        $bcaNumber = $topupConfig['bca']['number'];
        $bcaName = $topupConfig['bca']['name'];
        $mandiriNumber = $topupConfig['mandiri']['number'];
        $mandiriName = $topupConfig['mandiri']['name'];

        return view('topups.create', compact('bcaNumber', 'bcaName', 'mandiriNumber', 'mandiriName'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:10000'],
            'rekening_tujuan' => ['required', 'string', 'max:255'],
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('topups', 'public');
        }

        $request->user()->topups()->create([
            'amount' => $data['amount'],
            'rekening_tujuan' => $data['rekening_tujuan'],
            'bukti_pembayaran' => $data['bukti_pembayaran'],
            'status' => Topup::STATUS_PENDING,
        ]);

        return redirect()->route('topups.index')->with('status', 'Permintaan topup berhasil dikirim. Menunggu persetujuan admin.');
    }
}

