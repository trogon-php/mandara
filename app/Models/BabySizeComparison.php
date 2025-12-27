<?php

namespace App\Models;

class BabySizeComparison extends BaseModel
{
    protected $fillable = [
        'week',
        'comparison_one',
        'comparison_one_url',
        'comparison_two',
        'comparison_two_url',
        'comparison_three',
        'comparison_three_url',
        'length',
        'weight',
        'milestone_remarks',
    ];
}
