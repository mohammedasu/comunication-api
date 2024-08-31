<?php

namespace App\Services;

use App\Repositories\ArticleRepository;

class ArticleService
{
    protected $repository;
    protected $community_map_service;
    protected $sub_speciality_map_service;

    public function __construct()
    {
        $this->repository = new ArticleRepository();
    }

    public function show($params)
    {
        return $this->repository->findByMultipleFields(['id' => $params['id']]);
    }
}
