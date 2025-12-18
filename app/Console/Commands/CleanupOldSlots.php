<?php

namespace App\Console\Commands;

use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupOldSlots extends Command
{
    protected $signature = 'slots:cleanup 
                            {--days=90 : Delete slots older than X days}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Cleanup old slots that are no longer needed';

    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Finding slots older than {$cutoffDate->format('Y-m-d')}...");

        $query = Slot::where('date', '<', $cutoffDate)
            ->where('status', '!=', 'booked'); // Keep booked slots for history

        $count = $query->count();

        if ($count === 0) {
            $this->info('No old slots to cleanup.');

            return 0;
        }

        if ($dryRun) {
            $this->info("Would delete {$count} old slots (dry-run mode)");

            return 0;
        }

        if ($this->confirm("Delete {$count} old slots?", true)) {
            $deleted = $query->delete();
            $this->info("✅ Deleted {$deleted} old slots");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return 0;
    }
}
