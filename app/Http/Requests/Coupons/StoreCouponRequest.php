<?php

namespace App\Http\Requests\Coupons;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'integer|exists:packages,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'This coupon code already exists.',
            'code.max' => 'Coupon code must not exceed 50 characters.',
            'title.required' => 'Title is required.',
            'title.max' => 'Title must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'discount_type.required' => 'Discount type is required.',
            'discount_type.in' => 'Discount type must be either percentage or fixed.',
            'discount_value.required' => 'Discount value is required.',
            'discount_value.numeric' => 'Discount value must be a number.',
            'discount_value.min' => 'Discount value must be at least 0.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date must be today or later.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after start date.',
            'usage_limit.integer' => 'Usage limit must be a number.',
            'usage_limit.min' => 'Usage limit must be at least 1.',
            'per_user_limit.integer' => 'Per user limit must be a number.',
            'per_user_limit.min' => 'Per user limit must be at least 1.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
            'package_ids.array' => 'Package IDs must be an array.',
            'package_ids.*.integer' => 'Each package ID must be a number.',
            'package_ids.*.exists' => 'Selected package does not exist.',
            'user_ids.array' => 'User IDs must be an array.',
            'user_ids.*.integer' => 'Each user ID must be a number.',
            'user_ids.*.exists' => 'Selected user does not exist.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            // Validate percentage discount value
            if (isset($data['discount_type']) && $data['discount_type'] === 'percentage' && isset($data['discount_value']) && $data['discount_value'] > 100) {
                $validator->errors()->add('discount_value', 'Percentage discount cannot exceed 100%.');
            }
        });
    }
}
