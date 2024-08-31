<?php

namespace App\Repositories;

use App\Models\Cases;

class CaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Cases();
    }

    public function fetch($where)
    {
        return $this->model->where($where)->first();
    }
}
