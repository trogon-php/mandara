<?php

namespace App\Models;

use App\Models\BaseModel;

class QaCategory extends BaseModel
{
    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function questions()
    {
        return $this->hasMany(QaQuestion::class, 'category_id', 'id');
    }
}
