<?php

namespace App\Models;


class MemoryJournal extends BaseModel
{
    protected $fillable = [
        'user_id',
        'date',
        'image',
        'content',
    ];
    
    protected $casts = [
        'user_id' => 'integer',
        'date' => 'date',
    ];

    // Define file fields for image uploads
    protected $fileFields = [
        'image' => [
            'folder' => 'memory_journals',
            'preset' => 'memory_journals_image',
            'single' => true, // single image
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
