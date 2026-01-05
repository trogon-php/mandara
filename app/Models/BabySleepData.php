<?php

namespace App\Models;

use App\Models\BaseModel;

class BabySleepData extends BaseModel
{
    protected $table = 'baby_sleep_data';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
