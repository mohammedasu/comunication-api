<?php

namespace App\Repositories;

use App\Models\DataFilter;
use App\Constants\Constants;

class DataFilterRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new DataFilter();
    }

    public function findByMultipleFields($where_clause, $multiple = false)
    {
        if ($multiple) {
            return $this->model->where($where_clause)->paginate(Constants::PAGINATION_LENGTH);
        } else {
            return $this->model::withTrashed()->where($where_clause)->first();
        }
    }
}
