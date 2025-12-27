<?php

namespace App\Http\Requests\EstoreCategories;

use App\Http\Requests\BaseRequest;

class StoreEstoreCategoryRequest extends BaseRequest
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
            'status' => 'required|in:1,0',
        ];
    }
}