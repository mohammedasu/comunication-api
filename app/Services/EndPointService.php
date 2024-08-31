<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Repositories\EndPointRepository;

class EndPointService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new EndPointRepository();
    }

    public function getWhatsappEndPoint()
    {
        try {
            return $this->repository->getWhatsappEndPoint();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
