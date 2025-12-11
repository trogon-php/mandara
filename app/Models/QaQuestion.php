<?php

namespace App\Models;

class QaQuestion extends BaseModel
{
    protected $casts = [
        'category_id' => 'integer',
        'user_id' => 'integer',
        'question_text' => 'string',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(QaCategory::class, 'category_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function answers()
    {
        return $this->hasMany(QaAnswer::class, 'question_id', 'id');
    }
    public function votes()
    {
        return $this->hasMany(QaVote::class, 'question_id', 'id');
    }
}
