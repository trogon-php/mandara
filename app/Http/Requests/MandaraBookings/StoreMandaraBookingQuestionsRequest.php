<?php

namespace App\Http\Requests\MandaraBookings;

use App\Http\Requests\BaseRequest;

class StoreMandaraBookingQuestionsRequest extends BaseRequest
{
    
    public function rules(): array
    {
       
        return [
            'question'              => 'required|string|max:255',
    
           // 'options'               => 'required|array|min:1',
            'options.*.option_text' => 'required|string|max:255',
    
            'require_remark'        => 'nullable|boolean',
        ];
    }
   
}