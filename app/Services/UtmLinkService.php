<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Repositories\UtmLinkRepository;
use App\Exceptions\CustomErrorException;

class UtmLinkService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new UtmLinkRepository();
    }

    /**
     * Function to store utmLinks
     */

    public function store($params)
    {
        $logChunks = array_chunk($params, config('constants.storing_chunk_size'));
        foreach ($logChunks as $logs) {
            $this->repository->insert($logs);
        }
    }
}
