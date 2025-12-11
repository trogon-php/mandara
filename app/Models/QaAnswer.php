<?php

namespace App\Models;


class QaAnswer extends BaseModel
{
    protected $fillable = [
        'question_id',
        'user_id',
        'answer_text',
        'status',
    ];

    protected $casts = [
        'question_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'string',
    ];

    // Relationships
    public function question()
    {
        return $this->belongsTo(QaQuestion::class, 'question_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
