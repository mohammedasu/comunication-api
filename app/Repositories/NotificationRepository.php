<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Notification();
    }

    public function getUnprocessedEngagement()
    {
        return $this->model->where('is_processed', 0)
            ->whereNotNull('scheduled_timestamp')
            ->where('scheduled_timestamp', '<=', NOW())
            ->get();
    }

    public function update($where, $params)
    {
        $entity = $this->model->where($where)->first();

        if (!empty($entity)) {
            $entity->update($params);
            return $entity->refresh();
        }
    }
}
