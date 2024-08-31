<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\NotificationService;
use App\Services\WhatsappLogService;

class ProcessWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $whatAppData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($whatAppData)
    {
        $this->whatAppData      = $whatAppData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        $data = $notificationService->whatsappQueue($this->whatAppData);
        if (!empty($data)) {
            $whatsappLogService = new WhatsappLogService();
            $whatsappLogService->store($data);
        }
    }
}
