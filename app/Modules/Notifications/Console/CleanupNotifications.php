<?php
namespace App\Modules\Notifications\Console;

use Illuminate\Console\Command;
use App\Modules\Notifications\Models\Notification;
use Carbon\Carbon;

class CleanupNotifications extends Command
{
    protected $signature = 'notifications:cleanup {--days=90}';
    protected $description = 'Clean up old notifications';

    public function handle()
    {
        $days = $this->option('days');
        $this->info("Cleaning up notifications older than {$days} days...");

        $date = Carbon::now()->subDays($days);

        // Delete read and dismissed notifications older than the specified days
        $count = Notification::where('status', '!=', 'unread')
                          ->where('created_at', '<', $date)
                          ->delete();

        $this->info("{$count} notifications were removed.");
    }
}
