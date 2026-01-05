<?php

namespace App\Models;

use App\Models\BaseModel;

class BabyHealthData extends BaseModel
{
    protected $table = 'baby_health_data';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
