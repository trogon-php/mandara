<?php

namespace App\Models;

use App\Models\BaseModel;

class FeedCategory extends BaseModel
{
    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    // Define relationships if needed
    // public function feeds()
    // {
    //     return $this->hasMany(Feed::class);
    // }
}
