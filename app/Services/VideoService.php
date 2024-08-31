<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Repositories\VideoRepository;

class VideoService
{
    protected $repository;
    protected $community_map_service;
    protected $sub_speciality_map_service;

    public function __construct()
    {
        $this->repository = new VideoRepository();
    }

    public function show($params)
    {
        return $this->repository->findByMultipleFields(['id' => $params['id']]);
    }
}
