<?php

namespace App\Repositories;

use App\Models\Newsletter;

class NewsletterRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Newsletter();
    }

    public function fetch($where)
    {
        return $this->model->where($where)->first();
    }
}
