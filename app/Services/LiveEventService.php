<?php

namespace App\Services;

use App\Exceptions\CustomErrorException;
use Illuminate\Support\Facades\Log;
use App\Repositories\LiveEventRepository;

class LiveEventService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new LiveEventRepository();
    }
    /**
     * Function to show LiveEvent
     */

    public function show($id)
    {
        Log::info('LiveEventService | show', ['id' => $id]);
        $data = $this->repository->fetch(['id' => $id]);
        if (!$data) {
            throw new CustomErrorException(null, 'Something Went Wrong in Fetching LiveEvent.', 500);
        }
        return $data;
    }
}
