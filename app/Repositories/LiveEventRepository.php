<?php

namespace App\Repositories;

use App\Models\LiveEvent;

class LiveEventRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new LiveEvent();
    }
    public function fetch($where)
    {
        return $this->model->where($where)->first();
    }
}
