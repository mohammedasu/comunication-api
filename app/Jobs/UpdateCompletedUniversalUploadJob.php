<?php

namespace App\Jobs;

use App\Models\RecentUniversalUploads;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateCompletedUniversalUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $logFile,$history;

    public function __construct($logFile,RecentUniversalUploads $history)
    {
        $this->logFile = $logFile;
        $this->history = $history;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            DB::beginTransaction();
            $this->history->update([
                'log_file' => $this->logFile,
                'status' => true
            ]);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::info($e);
        }
    }
}
