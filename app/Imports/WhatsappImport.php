<?php

namespace App\Imports;

use App\Services\NotificationService;
use App\Services\WhatsappTemplateService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WhatsappImport implements ToCollection, WithHeadings, WithHeadingRow
{

    use Importable, SkipsFailures;

    public function __construct($engagement)
    {
        $this->engagement = $engagement;
    }

    public function collection(Collection $collection)
    {
        $whatsappService            = new NotificationService();
        $WhatsappTemplateService    = new WhatsappTemplateService();

        $imageUrl = null;
        if ($this->engagement->image) {
            $imageUrl = config('constants.notification_path') . $this->engagement->image;
        }

        $this->engagement->content = $this->engagement->payload['content'] ?? null;
        $templateData = $WhatsappTemplateService->getTemplatesForWhatsApp($this->engagement, $imageUrl);

        if ($templateData['template']) {
            $templateData['engagementId'] = $this->engagement->id;
            $whatsappService->sendWhatsappToCsvMembers($collection, $templateData);
            unset($this->engagement->content);
            $this->engagement->count = count($collection);
            $this->engagement->save();
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function headings(): array
    {
        return [
            'id',
            'country_code',
            'fname',
            'mobile_number',
            'member_ref_no'
        ];
    }
}
