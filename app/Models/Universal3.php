<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Universal3 extends Model
{

    protected $table = 'universal3';
    protected $guarded = [];
    public $timestamps = true;

    protected $casts = [
        'sub_speciality' => 'array',
    ];
}
