<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class SendEngagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'engagement:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push and whatsapp notifications to selected members.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        $notificationService->sendScheduledEngagement();
        echo "Notification sent successfully";
        return 0;
    }
}
