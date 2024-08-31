<?php

namespace App\Repositories;

use App\Models\RecentUniversalUploads;
use App\Constants\Constants;
use App\Helpers\UtilityHelper;

class UniversalMemberRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new RecentUniversalUploads();
    }

    public function generateReferenceNumber()
    {
        return 'NOTI' . UtilityHelper::generateString();
    }

    public function fetch($where) {
        return $this->model->where($where)->get();
    }

    public function fetchWithType($where, $type) {
        return $this->model->where($where)->where($type)->first();
    }

    public function create($request) {
        return $this->model->create($request);
    }

    public function update($where,$request) {
        $entity = $this->model->where($where)->first();
        
        if (!empty($entity)) {
            $entity->update($request);
            return $entity->refresh();
        }
    }

    public function destroy($where)
    {
        return $this->model->where($where)->delete();
    }

    public function findwithTrash($template_ref_no) {
        return $this->model::withTrashed()->where('template_ref_no', $template_ref_no)->first();
    }

    public function restore($email_template)
    {
        $email_template->restore();
        return $email_template;
    }
}
