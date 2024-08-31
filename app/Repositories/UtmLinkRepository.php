<?php

namespace App\Repositories;

use App\Models\UtmLinkDetail;
use App\Constants\Constants;

class UtmLinkRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new UtmLinkDetail();
    }

    public function insert($params)
    {
        return $this->model->insert($params);
    }
}
