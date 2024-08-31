<?php

namespace App\Repositories;

use App\Models\Video;
use App\Constants\Constants;

class VideoRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Video();
    }

    public function findByMultipleFields($where_clause, $multiple = false)
    {
        if ($multiple) {
            return $this->model->where($where_clause)->paginate(Constants::PAGINATION_LENGTH);
        } else {
            return $this->model->where($where_clause)->first();
        }
    }
}
