<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum  extends Model
{
    protected $table = 'partner_divisions';
    public function forumName()
    {
        return $this->hasOne(Forum::class, 'id', 'partner_division_id');
    }
}
