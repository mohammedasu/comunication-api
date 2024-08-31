<?php


namespace App\Services;

use App;
use App\Exceptions\ServiceErrorException;
use App\WhatsappLog;
use App\Services\Traits\ResponseCodeTrait;
use AppLog;
use App\Registry\SmsRegistry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Registry\EmailRegistry;

class CommunicationService
{
    protected $entity;
    protected $repository;
    protected $resource;
    private $sms_registry;
    private $email_registry;

    use ResponseCodeTrait;

    public function __construct()
    {
        $this->sms_registry = app()->make(SmsRegistry::class);
        $this->email_registry = app()->make(EmailRegistry::class);
    }

    /*
    |--------------------------------------------------------------------------
    | @param array $fields Table fields to pass rules
    | @return array
    | Set validation rules
    |--------------------------------------------------------------------------
    */
    public function getRules($fields = array())
    {
        $rules = [
            // 'mobile_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'country_code' => '',
            'mobile_number' => '',
            'template_id' => '',
            'sms_body' => '',
            'to_email' => '',
            'subject' => '',
            'html_body' => '',
        ];

        foreach ($fields as $field) {
            if (isset($rules[$field])) {
                $rules[$field] = 'required|' . $rules[$field];
            }
        }
        return $rules;
    }

    /*
    |--------------------------------------------------------
    |Function to send SMS
    |@request array $params
    |@return json $response
    |--------------------------------------------------------
    */

    public function sendSms($params)
    {
        AppLog::info('Communication Service', 'sendSms', 'Input', [$params]);

        //get transport for the SMS
        //for now we have only 1 transport - SNS

        $sms_transport = 'SNS'; // aws sns

        $response = $this->sms_registry->get($sms_transport)->request($params);

        AppLog::info('Communication Service', 'sendSms', 'Output', [$response]);

        if ($response) {
            $response = self::getResponseCode(1);
        } else {
            $response = self::getResponseCode(201);
        }

        return $response;
    }

    /*
    |--------------------------------------------------------
    |Function to send whatsapp message
    |@request array $params
    |@return json $response
    |--------------------------------------------------------
    */

    public function sendWhatsappMessage($params)
    {
        AppLog::info('Communication Service', 'sendWhatsappMessage', 'Input', [$params]);

        $response =  Http::withHeaders(['Authentication' => Config::get('services.whatsapp.key')])->post(Config::get('services.whatsapp.url'), $params['sms_body']);

        $api_response = $response->json();

        AppLog::info('Communication Service', 'sendWhatsappMessage', 'Output', [$api_response]);

        if (isset($api_response['statusCode']) && $api_response['statusCode'] == 200) {
            //update the response
            $whatsapp_log = WhatsappLog::find($params['whatsapp_log_id']);

            $whatsapp_log->response = $api_response;

            if (isset($api_response['mid'])) {
                $whatsapp_log->mid = $api_response['mid'];
            }
            $whatsapp_log->save();

            $response = self::getResponseCode(1);
        } else {
            $response = self::getResponseCode(201);
        }
        return $response;
    }

    /*
    |--------------------------------------------------------
    |Function to send email
    |@request array $params
    |@return json $response
    |--------------------------------------------------------
    */

    public function sendEmail($params)
    {
        AppLog::info('Communication Service', 'sendEmail', 'Input', [$params]);

        $mail_transport = 'SES';
        $response = $this->email_registry->get($mail_transport)->request($params);

        AppLog::info('Communication Service', 'sendEmail', 'Output', [$response]);

        if ($response) {
            $response = $this->getResponseCode(1);
        } else {
            $response = $this->getResponseCode(201);
        }

        return $response;
    }
}

