<?php

namespace App\Models;

use App\Models\BaseModel;

class UserHealthData extends BaseModel
{
    protected $table = 'user_health_data';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
