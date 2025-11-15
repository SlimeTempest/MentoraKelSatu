<?php

namespace App\Console\Commands;

use App\Services\JobExpirationService;
use Illuminate\Console\Command;

class ExpireJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menandai job yang sudah melewati deadline sebagai expired dan mengembalikan saldo';

    /**
     * Execute the console command.
     */
    public function handle(JobExpirationService $service): int
    {
        $expiredCount = $service->expireJobs();

        if ($expiredCount > 0) {
            $this->info("Berhasil menandai {$expiredCount} job sebagai expired dan mengembalikan saldo ke creator.");
        } else {
            $this->info('Tidak ada job yang perlu di-expire.');
        }

        return Command::SUCCESS;
    }
}
