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
            'offer_price' => 'nullable|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ];
    }
}