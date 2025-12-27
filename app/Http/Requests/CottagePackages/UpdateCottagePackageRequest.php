<?php

namespace App\Http\Requests\CottagePackages;

use App\Http\Requests\BaseRequest;

class UpdateCottagePackageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cottage_category_id' => 'required|exists:cottage_categories,id',
            'price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'booking_amount' => 'required|numeric|min:0',
            'tax_included' => 'required|in:0,1',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ];
    }
}