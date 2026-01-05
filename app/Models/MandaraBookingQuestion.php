<?php

namespace App\Models;



class MandaraBookingQuestion extends BaseModel
{
    protected $fillable = [
        'question',
        'options',
        'require_remark',
    ];

    protected $casts = [
        'options' => 'array',
        'require_remark' => 'boolean',
    ];

    public function getNormalizedOptionsAttribute(): array
    {
        if (!is_array($this->options)) {
            return [];
        }

        return collect($this->options)->map(fn ($opt) => [
            'value' => $opt['option_text'],
            'label' => $opt['option_text'],
        ])->all();
    }
   
}
