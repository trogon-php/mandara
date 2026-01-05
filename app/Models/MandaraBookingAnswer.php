<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandaraBookingAnswer extends Model
{
    protected $table = 'mandara_booking_answers';

    protected $fillable = [
        'user_id',
        'booking_id',
        'question_id',
        'answer',
        'remarks',
    ];

   public $timestamps = true;
}
