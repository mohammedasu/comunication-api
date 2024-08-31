<?php

namespace App\Services;

use App\Services\Contracts\SmsTransport;
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Config;
use Aws\Credentials\CredentialProvider;
use AppLog;

class SNSService implements SmsTransport
{
    public function request(array $params)
    {
        $provider = CredentialProvider::assumeRoleWithWebIdentityCredentialProvider();
        $provider = CredentialProvider::memoize($provider);
        $SnSclient = new SnsClient([
            'region' => 'ap-south-1',
            'version' => '2010-03-31',
            'credentials' => [
                'key'    => Config::get('services.sns.key'),
                'secret' => Config::get('services.sns.secret'),
            ],
        ]);

        try {
            $country_code = $params['country_code'];
            $phone_number = $params['country_code'] . $params['mobile_number'];

            $result_publish = $SnSclient->publish([
                'Message' => $params['sms_body'],
                'PhoneNumber' => $phone_number,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SenderID' => [
                        'DataType' => 'String',
                        'StringValue' => config('constants.sender_id'),
                    ],
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => config('constants.sms_type_transactional'),
                    ],
                    'AWS.MM.SMS.EntityId' => [
                        'DataType' => 'String',
                        'StringValue' => config('constants.entity_id'),
                    ],
                    'AWS.MM.SMS.TemplateId' => [
                        'DataType' => 'String',
                        'StringValue' => $params['template_id'],
                    ],

                ]
            ]);

            $messageId = $result_publish['MessageId'];
            $response = $messageId;
        } catch (AwsException $e) {
            $response = false;
            AppLog::info('Failed Message', 'sendSMSMessage', 'Output', [$e->getMessage()]);
        }
        return $response;
    }
}
