<?php

namespace App\Repositories;

use App\Models\EndPoint;
use App\Constants\Constants;

class EndPointRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new EndPoint();
    }

    public function getWhatsappEndPoint()
    {
        return $this->model->where('endpoint_name', Constants::WHATSAPP_ENDPOINT)->first();
    }
}
