<?php
namespace App\Modules\Notifications\Console;

use Illuminate\Console\Command;
use App\Modules\Notifications\Services\EmailService;

class SendDailyDigest extends Command
{
    protected $signature = 'notifications:send-daily-digest';
    protected $description = 'Send daily notification digest emails to users';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $this->info('Sending daily notification digests...');

        $this->emailService->sendDigest('daily');

        $this->info('Daily notification digests sent successfully!');
    }
}
