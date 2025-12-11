<?php

namespace App\Models;

use App\Models\BaseModel;

class LoginAttempt extends BaseModel
{
    protected $casts = [
        'user_id' => 'integer',
        'email' => 'string',
        'country_code' => 'string',
        'phone' => 'string',
        'channel' => 'string',
        'otp_code' => 'string',
        'ip_address' => 'string',
        'user_agent' => 'string',
        'status' => 'string', // enum('pending', 'verified', 'failed', 'expired')
        'remarks' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

