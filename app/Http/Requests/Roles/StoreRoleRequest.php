<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\BaseRequest;

class StoreRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:0,1',
        ];
    }
}
