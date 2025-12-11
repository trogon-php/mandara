<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Enums\Role as RoleEnum;

class Role extends BaseModel
{
    // Role constants
    const ADMIN     = RoleEnum::ADMIN->value;
    const TUTOR     = RoleEnum::TUTOR->value;
    const CLIENT    = RoleEnum::CLIENT->value;
    
    protected $casts = [
        'rating'        => 'integer',
        'status'        => 'integer',
        'profile_image' => 'string',
    ];

    
    protected $fileFields = [
        'profile_image' => [
            'folder' => 'roles', // folder in uploads
            'type' => 'roles_profile_image', //preset name
            'single' => true, // single image only
        ],
    ];
}
