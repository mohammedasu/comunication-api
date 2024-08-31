<?php

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDataFilterWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateData;
    protected $start;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($templateData, $start)
    {
        $this->templateData     = $templateData;
        $this->start            = $start;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        $notificationService->sendWhatsappToDataFilterMembers($this->templateData, $this->start);
    }
}
