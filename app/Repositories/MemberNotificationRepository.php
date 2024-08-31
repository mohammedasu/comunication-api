<?php

namespace App\Repositories;

use App\Models\MemberNotification;

class MemberNotificationRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new MemberNotification();
    }

    public function insert($request)
    {
        return $this->model->insert($request);
    }
}
