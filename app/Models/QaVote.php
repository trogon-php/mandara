<?php

namespace App\Models;

use App\Models\BaseModel;

class QaVote extends BaseModel
{
    protected $casts = [
        'question_id' => 'integer',
        'user_id' => 'integer',
        'vote_type' => 'string',
    ];
    // Relationships
    public function question()
    {
        return $this->belongsTo(QaQuestion::class, 'question_id', 'id');
    }
}
