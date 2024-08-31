<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    public $timestamps = true;

    protected $table = 'whatsapp_log';

    protected $fillable = [
        'type',
        'live_event_member_id',
        'member_id',
        'response',
        'whatsapp_template_id',
        'reference_type', 'reference_id', 'engagement_id'
    ];
}
