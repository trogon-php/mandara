<?php

namespace App\Http\Requests\MandaraBookings;

use App\Http\Requests\BaseRequest;

class UpdateMandaraBookingQuestionsRequest extends BaseRequest
{
   
    public function rules(): array
    {
        return [
            'question'              => 'sometimes|required|string|max:255',

           // 'options'               => 'sometimes|required|array|min:1',
            'options.*.option_text' => 'required|string|max:255',

            'require_remark'        => 'sometimes|nullable|boolean',
        ];
    }
   
}