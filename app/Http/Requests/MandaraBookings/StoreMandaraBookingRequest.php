<?php

namespace App\Http\Requests\MandaraBookings;

use App\Http\Requests\BaseRequest;

class StoreMandaraBookingRequest extends BaseRequest
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
        // used only for lookup, not storage
        'phone' => 'nullable|string',
        'email' => 'nullable|email',
            
        'user_id' => 'nullable|exists:users,id',
        'booking_number' => 'nullable|string',
        'date_from' => 'required|date',
        'date_to' => 'required|date',
        'is_delivered' => 'required|boolean',
        'delivery_date' => 'nullable|date',
        'additional_note' => 'nullable|string',
        ];
    }
}