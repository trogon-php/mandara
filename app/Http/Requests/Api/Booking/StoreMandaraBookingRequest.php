<?php

namespace App\Http\Requests\Api\Booking;

use App\Http\Requests\Api\BaseApiRequest;

class StoreMandaraBookingRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'is_delivered' => 'required|in:0,1',
            'delivery_date' => 'required|date',
            'cottage_package_id' => 'required|exists:cottage_packages,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'additional_note' => 'nullable|string|max:1000',
        ];
    }
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'is_delivered.required' => 'Delivery status is required.',
            'is_delivered.in' => 'Delivery status must be either 0 or 1.',
            'delivery_date.required' => 'Delivery date is required.',
            'delivery_date.date' => 'Delivery date must be a valid date.',
            'cottage_package_id.required' => 'Cottage package is required.',
            'cottage_package_id.exists' => 'Cottage package does not exist.',
            'date_from.required' => 'Date from is required.',
            'date_from.date' => 'Date from must be a valid date.',
            'date_to.required' => 'Date to is required.',
            'date_to.date' => 'Date to must be a valid date.',
            'additional_note.string' => 'Additional note must be a valid string.',
            'additional_note.max' => 'Additional note cannot exceed 1000 characters.',
        ];
    }
}
