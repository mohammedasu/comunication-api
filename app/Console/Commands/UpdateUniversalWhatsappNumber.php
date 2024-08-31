<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsappLogService;
use App\Services\Universal3Service;
use Carbon\Carbon;

class UpdateUniversalWhatsappNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'universal-whatsapp:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push and whatsapp notifications to selected members.';

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
        $whatsappLogService = new WhatsappLogService();
        $universal3Service = new Universal3Service();
        $data = $whatsappLogService->getLastNdaysLogs(config('constants.days_for_update_UWN'));
        $chunks = $data->chunk(config('constants.chunk_size'));
        foreach ($chunks as $value) {
            $universal3Service->updateUniversal3WhatsappNumber($value);
        }
        echo "Whatsapp Number has been updated successfully.";
        return 0;
    }
}
