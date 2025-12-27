<?php

namespace App\Http\Requests\EstoreProducts;

use App\Http\Requests\BaseRequest;

class UpdateEstoreProductRequest extends BaseRequest
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
            'category_id' => 'required|exists:estore_categories,id',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:1,0',
        ];
    }
}