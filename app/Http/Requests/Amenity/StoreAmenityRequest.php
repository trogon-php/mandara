<?php

namespace App\Http\Requests\Amenity;

use App\Http\Requests\BaseRequest;

class StoreAmenityRequest extends BaseRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('status')) {
            $status = $this->input('status');
            $this->merge([
                'status' => $status === 'active' ? 1 : ($status === 'inactive' ? 0 : $status),
            ]);
        }
    }
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:1,0',

            //  Allow repeater data
        'options' => 'nullable|array',

        //  Amenity items validation
        'options.*.title' => 'required|string|max:255',
        'options.*.description' => 'nullable|string',
        'options.*.duration_minutes' => 'nullable|integer',
        
        'options.*.price' => 'nullable|numeric',
        'options.*.status' => 'required|in:active,inactive',
        ];
    }
}