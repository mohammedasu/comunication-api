<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataFilter extends Model
{

    use SoftDeletes;
    protected $guarded = [];
    protected $table = 'data_filters';
    public $timestamps = true;
    protected $casts = [
        'universal_filters' => 'array',
        'member_filters' => 'array',
        'live_event_filters' => 'array',
    ];
}
