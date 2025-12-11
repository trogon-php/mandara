<?php

namespace App\Http\Requests\Cottages;

use App\Http\Requests\BaseRequest;

class StoreCottageRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'cottage_category_id' => 'required|exists:cottage_categories,id',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'capacity' => 'nullable|integer|min:1',
            'bedrooms' => 'nullable|integer|min:1',
            'bathrooms' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ];
    }
}