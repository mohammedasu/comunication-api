<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter  extends Model
{
    public function forumName()
    {
        return $this->hasOne(Forum::class, 'id', 'partner_division_id');
    }
}
