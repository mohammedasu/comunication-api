<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\NotificationService;

class ProcessPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $memberData;
    protected $templateData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($memberData, $templateData)
    {
        $this->memberData   = $memberData;
        $this->templateData = $templateData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        $notificationService->pushNotificationQueue($this->memberData, $this->templateData);
    }
}
