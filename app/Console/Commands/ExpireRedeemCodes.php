<?php

namespace App\Console\Commands;

use App\Services\RedeemCodeExpirationService;
use Illuminate\Console\Command;

class ExpireRedeemCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redeem-codes:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kembalikan saldo ke creator untuk redeem code yang sudah expired dan belum di-claim';

    /**
     * Execute the console command.
     */
    public function handle(RedeemCodeExpirationService $service): int
    {
        $expiredCount = $service->expireRedeemCodes();

        if ($expiredCount > 0) {
            $this->info("Berhasil mengembalikan saldo untuk {$expiredCount} redeem code yang expired.");
        } else {
            $this->info('Tidak ada redeem code yang perlu di-expire.');
        }

        return Command::SUCCESS;
    }
}

