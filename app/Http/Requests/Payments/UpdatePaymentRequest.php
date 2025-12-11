<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'order_id' => 'required|integer|exists:orders,id',
            'user_id' => 'required|integer|exists:users,id',
            'package_id' => 'required|integer|exists:packages,id',
            'amount_total' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'transaction_id' => 'nullable|string|max:100|unique:user_payments,transaction_id,' . request()->route('payment'),
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'Please select an order.',
            'order_id.exists' => 'The selected order does not exist.',
            'user_id.required' => 'Student information is required. Please select an order first.',
            'user_id.exists' => 'The student does not exist.',
            'package_id.required' => 'Please select a package.',
            'package_id.exists' => 'The selected package does not exist.',
            'amount_total.required' => 'Total amount is required.',
            'amount_total.numeric' => 'Total amount must be a number.',
            'amount_total.min' => 'Total amount must be at least 0.',
            'amount_paid.required' => 'Paid amount is required.',
            'amount_paid.numeric' => 'Paid amount must be a number.',
            'amount_paid.min' => 'Paid amount must be at least 0.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'transaction_id.unique' => 'This transaction ID already exists.',
            'transaction_id.max' => 'Transaction ID cannot exceed 100 characters.',
            'remarks.max' => 'Remarks cannot exceed 1000 characters.',
        ];
    }
}
