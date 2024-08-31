<?php

namespace App\Services;

use App\Repositories\Universal3Repository;

class Universal3Service
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new Universal3Repository();
    }

    public function updateUniversal3WhatsappNumber($WhatsappLogs)
    {
        foreach ($WhatsappLogs as $value) {
            if ($value && $value->reference_id && $value->reference_type = 'member' && $value->notification_attributes) {
                $res = json_decode($value->response);
                $status = json_decode($value->notification_attributes)->code;
                if (isset($res->whatsappNumber) && ($status == 101 || $status == 102)) {
                    $data = $this->repository->fetch(['member_ref_no' => $value->reference_id]);
                    if ($data && $data->whatsapp_number == null) {
                        $data->whatsapp_number = $res->whatsappNumber;
                        $data->save();
                    }
                }
            }
        }
    }
}
