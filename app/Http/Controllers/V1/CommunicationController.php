<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\ValidationFailedException;
use App\Http\Controllers\Controller;
use App\Jobs\SendSms;
use App\Services\Traits\ResponseCodeTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\CommunicationService;
use App\Jobs\SendWhatsappMessage;

class CommunicationController extends Controller
{
    private $communication_service;
    use ResponseCodeTrait;

    public function __construct(CommunicationService $communication_service)
    {
        $this->communication_service = $communication_service;
    }

    public function sendSms(Request $request)
    {
        $required = ['country_code', 'mobile_number', 'template_id', 'sms_body'];
        $this->validate($request->all(), $this->communication_service->getRules($required));

        $request_data = $request->all();

        if (isset($request_data['push_to_queue']) && $request_data['push_to_queue'] == true) {
            dispatch(new SendSms($request_data['country_code'], $request_data['mobile_number'], $request_data['template_id'], $request_data['sms_body']))->onQueue('sms');
            $response = self::getResponseCode(1);
        } else {
            $response = $this->communication_service->sendSms($request->all());
        }
        return $this->response($response);
    }

    public function sendWhatsappMessage(Request $request)
    {
        $required = ['mobile_number', 'sms_body', 'whatsapp_log_id'];
        $this->validate($request->all(), $this->communication_service->getRules($required));

        $request_data = $request->all();

        if (isset($request_data['push_to_queue']) && $request_data['push_to_queue'] == true) {
            dispatch(new SendWhatsappMessage($request_data['mobile_number'], $request_data['sms_body'], $request_data['whatsapp_log_id']))->onQueue('whatsapp');
            $response = self::getResponseCode(1);
        } else {
            $response = $this->communication_service->sendWhatsappMessage($request->all());
        }
        return $this->response($response);
    }

    public function sendEmail(Request $request)
    {
        $required = ['to_email', 'subject', 'html_body'];
        $this->validate($request->all(), $this->communication_service->getRules($required));

        $response = $this->communication_service->sendEmail($request->all());

        return $this->response($response);
    }
}
