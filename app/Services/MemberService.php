<?php

namespace App\Services;

use App\Repositories\MemberRepository;

class MemberService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new MemberRepository();
    }

    public function fetchMemberByDevice($device_type, $memberRefnumber)
    {
        try {
            return $this->repository->findByDevice($device_type, $memberRefnumber);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
