<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\CommunicationService;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $country_code;
    protected $mobile_number;
    protected $template_id;
    protected $sms_body;
    protected $ip_address,$mail_type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($country_code,$mobile_number,$template_id,$sms_body)
    {
        $this->country_code = $country_code;
        $this->mobile_number = $mobile_number;
        $this->template_id = $template_id;
        $this->sms_body = $sms_body;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        try{
            $params = [
                'country_code' => $this->country_code,
                'mobile_number' => $this->mobile_number,
                'template_id' => $this->template_id,
                'sms_body' => $this->sms_body
            ];

            $communication_service = new CommunicationService();
            return $communication_service->sendSms($params);

        }catch(\Exception $e){
            Log::info($e);
        }
    }
}
