<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Rencana;
use App\Models\BuktiSelfAssessment;
use App\Models\Pendampingan;
use App\Models\Pernyataan;
use App\Models\Permintaan;
use App\Models\Kemajuan;

class ClearSubmissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submissions:clear {--force : Force clear without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all submission data from database (Rencana, BuktiSelfAssessment, Pendampingan, Pernyataan, Permintaan, Kemajuan)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to clear ALL submission data? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Clearing all submission data...');

        // Disable foreign key checks based on database driver
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        try {
            // Clear Rencana (A5)
            if (Schema::hasTable('rencana')) {
                Rencana::query()->delete();
                $this->line('✓ Cleared: rencana table');
            }

            // Clear BuktiSelfAssessment (A6)
            if (Schema::hasTable('bukti_self_assessment')) {
                BuktiSelfAssessment::query()->delete();
                $this->line('✓ Cleared: bukti_self_assessment table');
            }

            // Clear Pendampingan (A7)
            if (Schema::hasTable('pendampingan')) {
                Pendampingan::query()->delete();
                $this->line('✓ Cleared: pendampingan table');
            }

            // Clear Pernyataan (A8)
            if (Schema::hasTable('pernyataan')) {
                Pernyataan::query()->delete();
                $this->line('✓ Cleared: pernyataan table');
            }

            // Clear Permintaan
            if (Schema::hasTable('permintaan')) {
                Permintaan::query()->delete();
                $this->line('✓ Cleared: permintaan table');
            }

            // Clear Kemajuan (progress tracking)
            if (Schema::hasTable('kemajuan')) {
                Kemajuan::query()->delete();
                $this->line('✓ Cleared: kemajuan table');
            }

            // Re-enable foreign key checks
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } elseif ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            }

            $this->newLine();
            $this->info('All submission data has been cleared successfully!');

            return 0;
        } catch (\Exception $e) {
            // Re-enable foreign key checks on error
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } elseif ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            }
            $this->error('Error clearing submissions: ' . $e->getMessage());
            return 1;
        }
    }
}
