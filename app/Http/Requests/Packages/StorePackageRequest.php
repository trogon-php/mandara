<?php

namespace App\Http\Requests\Packages;

use App\Http\Requests\BaseRequest;

class StorePackageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0|lt:price',
            'duration_days' => 'nullable|integer|min:1',
            'expire_date' => 'nullable|date|after:today',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Package title is required.',
            'title.string' => 'Package title must be a string.',
            'title.max' => 'Package title must not exceed 255 characters.',
            'description.string' => 'Description must be a string.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'offer_price.numeric' => 'Offer price must be a number.',
            'offer_price.min' => 'Offer price must be at least 0.',
            'offer_price.lt' => 'Offer price must be less than regular price.',
            'duration_days.integer' => 'Duration must be an integer.',
            'duration_days.min' => 'Duration must be at least 1 day.',
            'expire_date.date' => 'Expire date must be a valid date.',
            'expire_date.after' => 'Expire date must be after today.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
