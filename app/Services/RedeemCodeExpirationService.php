<?php

namespace App\Services;

use App\Models\RedeemCode;
use Illuminate\Support\Facades\DB;

class RedeemCodeExpirationService
{
    /**
     * Proses expired redeem codes dan kembalikan saldo ke creator
     * Hanya mengembalikan saldo untuk redeem code yang belum di-claim
     * 
     * @return int Jumlah redeem code yang di-expire
     */
    public function expireRedeemCodes(): int
    {
        // Cari redeem code yang sudah expired dan belum di-claim
        $expiredCodes = RedeemCode::where('is_claimed', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with('creator')
            ->get();

        $expiredCount = 0;

        foreach ($expiredCodes as $redeemCode) {
            DB::transaction(function () use ($redeemCode, &$expiredCount) {
                // Hanya kembalikan saldo jika ada creator dan belum di-claim
                if ($redeemCode->creator && !$redeemCode->is_claimed) {
                    $redeemCode->creator->increment('balance', $redeemCode->amount);
                    $expiredCount++;
                }
            });
        }

        return $expiredCount;
    }
}

