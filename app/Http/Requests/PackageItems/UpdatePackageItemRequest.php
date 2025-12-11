<?php

namespace App\Http\Requests\PackageItems;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdatePackageItemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'package_id' => 'required|integer|exists:packages,id',
            'item_type' => 'required|in:course',
            'item_id' => 'required|integer',
            'item_title' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->package_id && $this->item_type && $this->item_id) {
                // Check if item already exists in this package (excluding current record)
                $exists = \App\Models\PackageItem::where('package_id', $this->package_id)
                    ->where('item_type', $this->item_type)
                    ->where('item_id', $this->item_id)
                    ->where('id', '!=', $this->route('package_item')) // Exclude current record
                    ->exists();
                
                if ($exists) {
                    $validator->errors()->add('item_id', 'This item is already added to the selected package.');
                }

                // Validate item_id exists in the respective table
                $this->validateItemExists($validator);
            }
        });
    }

    protected function validateItemExists($validator)
    {
        $itemId = $this->item_id;

        // Since we only allow courses now, validate course exists
        if (!\App\Models\Course::where('id', $itemId)->exists()) {
            $validator->errors()->add('item_id', 'Selected course does not exist.');
        }
    }

    public function messages(): array
    {
        return [
            'package_id.required' => 'Package is required.',
            'package_id.exists' => 'Selected package does not exist.',
            'item_type.required' => 'Item type is required.',
            'item_type.in' => 'Item type must be course.',
            'item_id.required' => 'Item selection is required.',
            'item_id.integer' => 'Item ID must be a valid number.',
            'item_title.string' => 'Item title must be a valid string.',
            'item_title.max' => 'Item title cannot exceed 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
            'sort_order.integer' => 'Sort order must be a valid number.',
            'sort_order.min' => 'Sort order must be 0 or greater.',
        ];
    }
}