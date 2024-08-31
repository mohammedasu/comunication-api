<?php

namespace App\Services;

use App\Exceptions\CustomErrorException;
use App\Helpers\ApiResponse;
use App\Helpers\ImageHelper;
use App\Imports\UniversalMemberImport;
use App\Jobs\UniversalMembersUpload;
use App\Jobs\UpdateCompletedUniversalUploadJob;
use App\Repositories\UniversalMemberRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UniversalMemberService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new UniversalMemberRepository();
    }

    /**
     * Function to fetch Universal Member list
     * @param $request
     */

    public function uploadData()
    {
        Log::info('UniversalMemberService | uploadData');

        $data = $this->repository->fetch(['status' => 0]);
        if(count($data) > 0) {
            // throw new CustomErrorException(null, 'Something Went Wrong in Fetching Universal Members Sheet.', 500);
            foreach ($data as $value) {
                if(!empty($value->upload_file)) {
                    $logFile = time() . '-universal_upload-log.txt';
                    Storage::disk('s3')->prepend('Logs/UniversalUpload/' . $logFile, 'Logs');
                    $this->repository->update(['id' => $value->id],['status' => 2]);
                    Excel::queueImport(new UniversalMemberImport($logFile,$value), 'temp/' . $value->upload_file, 's3', \Maatwebsite\Excel\Excel::CSV)->chain([
                        new UpdateCompletedUniversalUploadJob($logFile,$value),
                    ]);
                }
            }
        }

        return true;
    }

    public function uploadUniversalMember($logFile,$collection,$history) {
        Log::info('UniversalMemberService | uploadUniversalMember');
        dispatch(new UniversalMembersUpload($logFile,$collection,$history));
    }
}
