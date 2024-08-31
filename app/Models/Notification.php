<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $table = 'engagements';

    protected $guarded = [];

    protected $casts = [
        'target_members' => 'array',
        'payload' => 'array',
    ];
}
