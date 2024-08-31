<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\UtmLinkService;

class ProcessUtmLinks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $whatAppData;
    protected $memberUtmLinks;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($whatAppData, $memberUtmLinks)
    {
        $this->whatAppData      = $whatAppData;
        $this->memberUtmLinks   = $memberUtmLinks;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        dispatch(new ProcessWhatsappMessage($this->whatAppData));
        $utmLinkService      = new UtmLinkService();
        $utmLinkService->store($this->memberUtmLinks);
    }
}
