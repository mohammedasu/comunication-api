<?php

namespace App\Services;

use App\Repositories\WhatsappLogRepository;

class WhatsappLogService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new WhatsappLogRepository();
    }

    public function store($params)
    {
        $logChunks = array_chunk($params, config('constants.storing_chunk_size'));
        foreach ($logChunks as $logs) {
            $this->repository->insert($logs);
        }
    }

    public function getLastNdaysLogs($n)
    {
        return $this->repository->getLastNdaysLogs($n);
    }
}
