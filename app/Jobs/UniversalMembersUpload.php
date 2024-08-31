<?php

namespace App\Jobs;

use App\Helpers\UtilityHelper;
use App\Models\RecentUniversalUploads;
use App\Models\Universal3;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UniversalMembersUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $logFile,$data,$history;
    public function __construct($logFile,$collection, RecentUniversalUploads $history)
    {
        $this->logFile = $logFile;
        $this->data = $collection;
        $this->history = $history;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->data as $index => $row) {
          
            $index += $this->history->rows_completed;
            $valid = $this->validateData($row,$index + 2);
            if(!$valid['status']){
                Log::info($valid['message']);
                Storage::disk('s3')->append('Logs/UniversalUpload/'.$this->logFile,$valid['message']);
                continue;
            }
            $universal_member = $valid['universal_member'];

            $fillable_values = [
                'mobile_number' => $row['mobile_number'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'speciality' => $row['speciality'],
                'sub_speciality' => $row['sub_speciality'],
                'country_code' => $row['country_code']?? '91',
                'email' => $row['email'],
                'data_source' => $row['data_source'],
                'alternate_number' => $row['alternate_number'],
                'whatsapp_number' => $row['whatsapp_number'],
                'user_status' => $row['user_status'],
                'digiMR_status' => $row['digimr_status'],
                'city' => $row['city'],
                'state' => $row['state'],
                'country' => $row['country'],
                'mobile_number_length' => strlen($row['mobile_number']),
                'zone' => $row['zone'],
                'tier' => $row['tier'],
                'metro' => $row['metro'],
                'class' => $row['class'],
            ];

            if (!$universal_member) {
                $fillable_values['user_status'] = 'universal';
                $fillable_values['member_ref_no'] = 'MRN'.UtilityHelper::generateString();
                Universal3::create($fillable_values);
            } else {
                $universal_member->update($fillable_values);
            }
        }
        Log::info(['Uploading into universal members completed for chunk']);
        $this->history->update([
            'status'         => 1,
            'rows_completed' => $this->history->rows_completed + count($this->data),
        ]);
    }

    public function validateData($row,$index)
    {
        $mobile_number = null;
        $email = null;
        if($row['first_name'] == null || trim($row['first_name'] == '')){
            return ['status' => false, 'message' => 'First Name missing at row : '.$index];
        }
        if($row['mobile_number'] && $row['mobile_number'] != null && trim($row['mobile_number']) != ''){
            $mobile_number = $row['mobile_number'];
        }
        if($row['email'] && $row['email'] != null && trim($row['email']) != ''){
            $email = $row['email'];
        }

        if($mobile_number == null){
            return ['status' => true, 'universal_member' => null, 'message' => 'Mobile Number and Email missing at row : '.$index];
        }

        $universal_member = Universal3::where(function($q) use ($mobile_number){
                            return $q->when($mobile_number != null,function($q) use ($mobile_number){
                                return $q->where('mobile_number',$mobile_number);
                            });
                        });

        if($email != null) {
            $universal_member = $universal_member->orWhere(function ($q) use ($email) {
                return $q->when($email != null, function ($q) use ($email) {
                    return $q->where('email', $email);
                });
            });
        }

        $universal_member = $universal_member->first();
        return ['status' => true, 'universal_member' => $universal_member];
    }
}
