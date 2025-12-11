<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'package_id' => 'required|integer|exists:packages,id',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
            'coupon_code' => 'nullable|string|max:50',
            'amount_total' => 'required|numeric|min:0',
            'amount_offer' => 'nullable|numeric|min:0',
            'amount_final' => 'required|numeric|min:0',
            'status' => 'required|in:pending,partially_paid,paid,cancelled,refunded',
            'order_number' => 'nullable|string|max:50|unique:orders,order_number,' . request()->route('order'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user does not exist.',
            'package_id.required' => 'Please select a package.',
            'package_id.exists' => 'The selected package does not exist.',
            'coupon_id.exists' => 'The selected coupon does not exist.',
            'amount_total.required' => 'Total amount is required.',
            'amount_total.numeric' => 'Total amount must be a number.',
            'amount_total.min' => 'Total amount must be at least 0.',
            'amount_final.required' => 'Final amount is required.',
            'amount_final.numeric' => 'Final amount must be a number.',
            'amount_final.min' => 'Final amount must be at least 0.',
            'status.required' => 'Order status is required.',
            'status.in' => 'Invalid order status.',
            'order_number.unique' => 'This order number already exists.',
        ];
    }
}
