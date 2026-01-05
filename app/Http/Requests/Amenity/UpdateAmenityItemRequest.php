<?php

namespace App\Http\Requests\Amenity;

use App\Http\Requests\BaseRequest;

class UpdateAmenityItemRequest extends BaseRequest
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
            'title' => 'sometimes|required|string|max:255',
            'amenity_id' => 'sometimes|required|exists:amenities,id',
            'description' => 'sometimes|nullable|string',
            'duration_minutes' => 'sometimes|required|integer|min:0',
            'duration_text' => 'sometimes|nullable|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:active,inactive',
        ];
    }
}