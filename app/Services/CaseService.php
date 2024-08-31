<?php

namespace App\Services;

use App\Exceptions\CustomErrorException;
use App\Repositories\CaseRepository;
use Illuminate\Support\Facades\Log;

class CaseService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new CaseRepository();
    }

    /**
     * Function to show Case
     * @param $request
     */

    public function show($id)
    {
        $where = ['id' => $id];
        $data = $this->repository->fetch($where);
        if (!$data) {
            throw new CustomErrorException(null, 'Something Went Wrong in Fetching Case.', 500);
        }

        return $data;
    }
}
