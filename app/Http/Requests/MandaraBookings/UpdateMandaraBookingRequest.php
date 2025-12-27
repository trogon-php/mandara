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
            'booking_number' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
            'cottage_package_id' => 'required|exists:cottage_packages,id',
            'date_from' => 'sometimes|required|date',
            'date_to' => 'sometimes|required|date',
            'is_delivered' => 'sometimes|required|in:0,1',
            'delivery_date' => 'sometimes|required|date',
            'remarks' => 'nullable|string|max:1000',
            'blood_group' => 'sometimes|required|string|max:10',
            'is_veg' => 'sometimes|required|in:0,1',
            'diet_remarks' => 'sometimes|nullable|string|max:1000',
            'address' => 'sometimes|required|string|max:500',
            'have_caretaker' => 'sometimes|required|in:0,1',
            'caretaker_name' => 'required_if:have_caretaker,1|nullable|string|max:255',
            'caretaker_age' => 'sometimes|required_if:have_caretaker,1|nullable|integer|min:1|max:150',
            'have_siblings' => 'sometimes|required|in:0,1',
            'sibling_names' => 'required_if:have_siblings,1|nullable|string|max:255',
            'sibling_ages' => 'sometimes|required_if:have_siblings,1|nullable|integer|min:1|max:150',
            'booking_amount' => 'sometimes|required|numeric|min:0',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'payable_amount' => 'required|numeric|min:0',
            'offer_amount' => 'sometimes|required|numeric|min:0',
            'booking_payment_status' => 'sometimes|required|in:0,1',
            'approval_status' => 'sometimes|required|in:0,1',
            'additional_note' => 'nullable|string|max:1000',
            'status' => 'sometimes|required|in:1,0',
        ];
    }
}