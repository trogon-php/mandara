<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Default: always authorize.
     * Override in child requests if needed.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Helper to get validated data with extra defaults merged in.
     */
    public function safeWith(array $extra = []): array
    {
        return array_merge($this->validated(), $extra);
    }
}
