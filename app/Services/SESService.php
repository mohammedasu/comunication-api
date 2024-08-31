<?php

namespace App\Services;

use AppLog;
use Aws\Credentials\CredentialProvider;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Config;
use App\Services\Contracts\EmailTransport;

class SESService implements EmailTransport
{
    public function request(array $params)
    {
        $provider = CredentialProvider::assumeRoleWithWebIdentityCredentialProvider();
        $provider = CredentialProvider::memoize($provider);

        $SesClient = new SesClient([
            'version' => '2010-12-01',
            'region'  => 'ap-south-1',
            'credentials' => [
                'key'    => Config::get('services.ses.key'),
                'secret' => Config::get('services.ses.secret'),
            ],

        ]);
        $to_emails = !empty($params['to_email']) ? $params['to_email'] : '';
        $sender_email = 'info@email.mymedisage.com';
        $sender_name = 'Medisage';
        $recipient_emails = [$to_emails];
        $subject = !empty($params['subject']) ? $params['subject'] : '';
        $html_body =  !empty($params['html_body']) ? $params['html_body'] : '';
        $char_set = 'UTF-8';

        try {
            $result = $SesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => $recipient_emails,
                ],
                'ReplyToAddresses' => [$sender_email],
                'Source' => "" . $sender_name . " <" . $sender_email . ">",
                'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => $char_set,
                            'Data' => $html_body,
                        ],
//                            'Text' => [
//                            'Charset' => $char_set,
//                            'Data' => $html_body,
//                            ],
                    ],
                    'Subject' => [
                        'Charset' => $char_set,
                        'Data' => $subject,
                    ],
                ],

            ]);
            $messageId = $result['MessageId'];

            $response = $messageId;
        } catch (AwsException $e) {
            $response = false;
        }
        return $response;
    }
}
