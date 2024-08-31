<?php

namespace App\Repositories;

use App\Models\WhatsappLog;
use Carbon\Carbon;

class WhatsappLogRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new WhatsappLog();
    }

    public function insert($params)
    {
        return $this->model->insert($params);
    }

    public function getLastNdaysLogs($n = 1)
    {
        $date = Carbon::now()->subDays($n);
        return $this->model->where('created_at', '>=', $date)->get();
    }
}
