<?php

namespace App\Repositories;

use App\Helpers\AudienceFilterHelper;
use App\Models\Universal3;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Universal3Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Universal3();
    }

    public function filterDataFromRepository($universal_filters, $member_filters, $live_event_filters, $action)
    {
        DB::enableQueryLog();

        Log::info('Universal3Repository | before calling audienceFilter');
        $filter_data = AudienceFilterHelper::audienceFilter($this->model->query(), $universal_filters, $member_filters, $live_event_filters);
        Log::info('Universal3Repository | after calling audienceFilter');

        if ($action == 'check') {
            //$count = $this->model->distinct('mobile_number')->count();
            $count = $filter_data->distinct('universal3.mobile_number')->count();
            $query_log = DB::getQueryLog();
            //dd($query_log);
            return $count;
        } elseif ($action == 'download') {
            $filter_data = $filter_data->select(
                'universal3.mobile_number',
                'universal3.member_ref_no',
                'universal3.first_name',
                'universal3.last_name',
                'universal3.email',
                'universal3.city',
                'universal3.speciality',
                'universal3.state',
                'universal3.reg_no',
                'universal3.reg_state',
                'universal3.country_code',
                DB::raw("'universal3.data_filter' as type")
            )
                ->get();
            Log::info('Universal3Repository | Data Filter count '.$filter_data->unique('mobile_number')->count());
            return $filter_data->unique('mobile_number');
        }
    }

    public function fetch($where, $multiple = false)
    {
        if ($multiple) {
            return $this->model->where($where)->get();
        } else {
            return $this->model->where($where)->first();
        }
    }
}
