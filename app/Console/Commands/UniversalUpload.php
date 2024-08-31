<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UniversalMemberService;

class UniversalUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'universal:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload Universal members.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $UniversalMemberService = new UniversalMemberService();
        $UniversalMemberService->uploadData();
        echo "Universal Data Upload successfully";
        return 0;
    }
}
