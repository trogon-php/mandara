<?php

namespace App\Models;

use App\Models\BaseModel;

class UserWeightData extends BaseModel
{
    protected $table = 'user_weight_data';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
