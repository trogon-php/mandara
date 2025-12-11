<?php

namespace App\Models;

use App\Models\BaseModel;

class Otp extends BaseModel
{
    
    protected $casts = [
        'expires_at'        => 'datetime',
        'verified_at'       => 'datetime'
    ];
}
