<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Repositories\NewsletterRepository;
use App\Exceptions\CustomErrorException;

class NewsletterService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new NewsletterRepository();
    }

    /**
     * Function to show Newsletter
     */

    public function show($id)
    {
        $data = $this->repository->fetch(['id' => $id]);
        if (!$data) {
            throw new CustomErrorException(null, 'Something Went Wrong in Fetching Newsletter.', 500);
        }
        return $data;
    }
}
