<?php

namespace App\Services;

use App\Exceptions\CustomErrorException;
use App\Repositories\MemberNotificationRepository;

class MemberNotificationService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new MemberNotificationRepository();
    }

    /**
     * Function to store Member Notification details
     * @param $request
     */

    public function store($params)
    {
        $data = $this->repository->insert($params);
        if (!$data) {
            throw new CustomErrorException(null, 'Something went wrong with storing Member Notification', 500);
        }

        return $data;
    }
}
