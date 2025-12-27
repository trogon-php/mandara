<?php

namespace App\Http\Requests\Api\Booking;

use App\Http\Requests\Api\BaseApiRequest;

class StoreMandaraBookingAdditionalRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'blood_group' => 'required|string|max:10',
            'is_veg' => 'required|in:0,1',
            'diet_remarks' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'pickup_address' => 'nullable|string|max:500',
            'husband_name' => 'nullable|string|max:255',
            'have_siblings' => 'required|in:0,1',
            'caretaker_name' => 'nullable|string|max:255',
            'caretaker_age' => 'nullable|integer|min:1|max:150',
        ];
    }
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'blood_group.required' => 'Blood group is required.',
            'blood_group.string' => 'Blood group must be a valid string.',
            'blood_group.max' => 'Blood group cannot exceed 10 characters.',
            'is_veg.required' => 'Vegetarian status is required.',
            'is_veg.in' => 'Vegetarian status must be either 0 or 1.',
            'diet_remarks.string' => 'Diet remarks must be a valid string.',
            'diet_remarks.max' => 'Diet remarks cannot exceed 1000 characters.',
            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a valid string.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'husband_name.string' => 'Husband name must be a valid string.',
            'husband_name.max' => 'Husband name cannot exceed 255 characters.',
            'have_caretaker.required' => 'Caretaker status is required.',
            'have_caretaker.in' => 'Caretaker status must be either 0 or 1.',
            'caretaker_name.required_if' => 'Caretaker name is required when caretaker is present.',
            'caretaker_name.string' => 'Caretaker name must be a valid string.',
            'caretaker_name.max' => 'Caretaker name cannot exceed 255 characters.',
            'caretaker_age.required_if' => 'Caretaker age is required when caretaker is present.',
            'caretaker_age.integer' => 'Caretaker age must be a valid number.',
            'caretaker_age.min' => 'Caretaker age must be at least 1.',
            'caretaker_age.max' => 'Caretaker age cannot exceed 150.',
        ];
    }
}
