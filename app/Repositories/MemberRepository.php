<?php

namespace App\Repositories;

use App\Models\Member;

class MemberRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Member();
    }

    public function findByDevice($device_type = null, $memberRefnumber)
    {
        $members = $this->model->query();
        if (!empty($memberRefnumber)) {
            $members->whereIn('member_ref_no', $memberRefnumber);
        }
        if ($device_type && $device_type != "all") {
            $members->where('device_type', $device_type);
        }
        return $members->whereNotNull(['device_token', 'device_type'])->get();
    }
}
