<?php

namespace App\Models;

use App\Models\BaseModel;

class UserSleepData extends BaseModel
{
    protected $table = 'user_sleep_data';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
