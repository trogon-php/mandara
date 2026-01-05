<?php

namespace App\Http\Requests\MandaraBookings;

use App\Http\Requests\BaseRequest;

class UpdateMandaraBookingRequest extends BaseRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('approval_status')) {
            $approval_status = $this->input('approval_status');
            $this->merge([
                'approval_status' => $approval_status === 'approved' ? 'approved' : ($approval_status === 'rejected' ? 'rejected' : 'pending'),
            ]);
        }
    }
    public function rules(): array
    {
        return [

            // Booking dates & delivery info
            'date_from'        => 'sometimes|required|date',
            'date_to'          => 'sometimes|required|date',
            'is_delivered'     => 'sometimes|required|in:0,1',
            'delivery_date'   => 'sometimes|nullable|date',
        
            // Additional / medical details
            'blood_group'      => 'sometimes|required|string|max:10',
            'is_veg'           => 'sometimes|required|in:0,1',
            'diet_remarks'     => 'sometimes|nullable|string|max:1000',
            'address'          => 'sometimes|required|string|max:500',
        
            // Caretaker details
            'have_caretaker'   => 'sometimes|required|in:0,1',
            'caretaker_name'   => 'nullable|required_if:have_caretaker,1|string|max:255',
            'caretaker_age'    => 'nullable|required_if:have_caretaker,1|integer|min:1|max:150',
        
            // Family details
            'have_siblings'    => 'sometimes|required|in:0,1',
            'husband_name'     => 'nullable|string|max:255',
        
            // Notes
            'additional_note'  => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}