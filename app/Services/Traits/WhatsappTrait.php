<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Http;
use App\Exceptions\CustomErrorException;

/**
 * Trait WhatsappTrait
 * @package App\Traits
 */
trait WhatsappTrait
{
    /**
     * Trait function to send a whatsapp message.
     */
    public function sendWhatsappMessage($data)
    {
        try {
            $templateId         = $data['templateId'];
            $recipientNumber    = $data['mobileNumber'];
            $parameterValues    = $data['contentParams'];
            $imageLink          = $data['imageLink'] ?? null;

            $endpoint = $this->endPointService->getWhatsappEndPoint();
            if ($imageLink != null) {
                $jsonVal = [
                    'message' => [
                        'channel' => "WABA",
                        'content' => [
                            "preview_url" => false,
                            "shorten_url" => false,
                            "type" => "MEDIA_TEMPLATE",
                            "mediaTemplate" => [
                                "templateId" => $templateId,
                                "media" => [
                                    "type" => "image",
                                    "url" => $imageLink
                                ],
                                "bodyParameterValues" => $parameterValues
                            ]
                        ],
                        "recipient" => [
                            "to" => $recipientNumber,
                            "recipient_type" => "individual"
                        ],
                        "sender" => [
                            "from" => $endpoint->sender_name
                        ],
                        "preferences" => [
                            "webHookDNId" => "1001"
                        ]
                    ],
                    "metaData" => [
                        "version" => "v1.0.9"
                    ]
                ];
            } else {
                $jsonVal = [
                    'message' => [
                        'channel' => "WABA",
                        'content' => [
                            "preview_url" => false,
                            "type" => "TEMPLATE",
                            "template" => [
                                "templateId" => $templateId,
                                "parameterValues" => $parameterValues
                            ]
                        ],
                        "recipient" => [
                            "to" => $recipientNumber,
                            "recipient_type" => "individual"
                        ],
                        "sender" => [
                            "from" => $endpoint->sender_name
                        ],
                        "preferences" => [
                            "webHookDNId" => "1001"
                        ]
                    ],
                    "metaData" => [
                        "version" => "v1.0.9"
                    ]
                ];
            }

            $res = Http::withHeaders(['Authentication' => $endpoint->key])->post($endpoint->url, $jsonVal);
            $responseBody = $res->json();
            return $responseBody;
        } catch (\Exception $e) {
            throw new CustomErrorException($e);
        }
    }
}
