<?php

namespace App\Models;

use App\Models\BaseModel;

class BabyWeightData extends BaseModel
{
    protected $table = 'baby_weight_data';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
