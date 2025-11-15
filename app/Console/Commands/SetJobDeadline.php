<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class SetJobDeadline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:set-deadline {job_id} {--yesterday : Set deadline jadi kemarin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set deadline job jadi kemarin untuk testing expired (bypass policy)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $jobId = $this->argument('job_id');
        $job = Job::find($jobId);

        if (!$job) {
            $this->error("Job dengan ID {$jobId} tidak ditemukan.");
            return Command::FAILURE;
        }

        if ($this->option('yesterday')) {
            $newDeadline = now()->subDay();
            $oldDeadline = $job->deadline ? $job->deadline->format('Y-m-d') : 'Tidak ada';

            $job->update(['deadline' => $newDeadline]);

            $this->info("✅ Deadline job #{$jobId} diubah:");
            $this->line("   Sebelum: {$oldDeadline}");
            $this->line("   Sesudah: {$newDeadline->format('Y-m-d')} (kemarin)");
            $this->newLine();
            $this->info("Sekarang buka halaman /jobs di browser untuk auto-expire.");
            $this->info("Atau jalankan: php artisan jobs:expire");

            return Command::SUCCESS;
        }

        // Show current deadline
        $this->info("Job #{$jobId}: {$job->title}");
        $this->line("Status: {$job->status}");
        $this->line("Deadline: " . ($job->deadline ? $job->deadline->format('Y-m-d') : 'Tidak ada'));
        $this->newLine();
        $this->info("Gunakan --yesterday untuk set deadline jadi kemarin");

        return Command::SUCCESS;
    }
}
