<?php

namespace App\Imports;

use App\Services\UniversalMemberService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UniversalMemberImport implements ToCollection, ShouldQueue,WithChunkReading, WithHeadingRow
{

    use Importable, SkipsFailures;

    public function __construct($logFile,$history)
    {
        $this->history = $history;
        $this->logFile = $logFile;
    }

    public function collection(Collection $collection)
    {
        $universal_member_service = new UniversalMemberService();
        $universal_member_service->uploadUniversalMember($this->logFile,$collection,$this->history);
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
