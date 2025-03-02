<?php
namespace App\Modules\Notifications\Console;

use Illuminate\Console\Command;
use App\Modules\Notifications\Services\EmailService;

class SendWeeklyDigest extends Command
{
    protected $signature = 'notifications:send-weekly-digest';
    protected $description = 'Send weekly notification digest emails to users';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $this->info('Sending weekly notification digests...');

        $this->emailService->sendDigest('weekly');

        $this->info('Weekly notification digests sent successfully!');
    }
}
