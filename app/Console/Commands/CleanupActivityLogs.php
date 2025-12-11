<?php

namespace App\Console\Commands;

use App\Modules\Logs\Services\ActivityLogService;
use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-logs:cleanup {--days=365 : Number of days to keep logs for} {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old activity logs to manage database size';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Activity Log Cleanup");
        $this->info("==================");
        $this->info("Keeping logs for the last {$days} days");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No logs will be deleted");
        }

        $cutoffDate = now()->subDays($days);
        $this->info("Cutoff date: {$cutoffDate->format('Y-m-d H:i:s')}");

        // Count logs that would be deleted
        $logsToDelete = \App\Modules\Logs\Models\ActivityLog::where('created_at', '<', $cutoffDate)->count();
        
        if ($logsToDelete === 0) {
            $this->info("No logs to delete.");
            return 0;
        }

        $this->info("Found {$logsToDelete} logs to delete.");

        if ($dryRun) {
            $this->info("Dry run completed. {$logsToDelete} logs would be deleted.");
            return 0;
        }

        if (!$this->confirm("Are you sure you want to delete {$logsToDelete} logs?")) {
            $this->info("Operation cancelled.");
            return 0;
        }

        $bar = $this->output->createProgressBar($logsToDelete);
        $bar->start();

        // Delete in chunks to avoid memory issues
        $deleted = 0;
        \App\Modules\Logs\Models\ActivityLog::where('created_at', '<', $cutoffDate)
            ->chunk(1000, function ($logs) use (&$deleted, $bar) {
                $logs->each(function ($log) {
                    $log->delete();
                });
                $deleted += $logs->count();
                $bar->advance($logs->count());
            });

        $bar->finish();
        $this->newLine();
        $this->info("Successfully deleted {$deleted} activity logs.");

        return 0;
    }
}
