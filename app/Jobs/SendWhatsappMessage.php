<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\CommunicationService;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $mobile_number;
    protected $sms_body;
    protected $whatsapp_log_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($mobile_number,$sms_body,$whatsapp_log_id)
    {
        $this->mobile_number = $mobile_number;
        $this->sms_body = $sms_body;
        $this->whatsapp_log_id = $whatsapp_log_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $params = [
                'mobile_number' => $this->mobile_number,
                'sms_body' => $this->sms_body,
                'whatsapp_log_id' => $this->whatsapp_log_id
            ];

            $communication_service = new CommunicationService();
            return $communication_service->sendWhatsappMessage($params);
        } catch (\Exception $e) {
            Log::info($e);
        }
    }
}
