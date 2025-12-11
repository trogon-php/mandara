<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // centralize access checks in middleware
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        
        // For enrollment errors, return a simple message
        if ($errors->has('course_id') && str_contains($errors->first('course_id'), 'Already enrolled')) {
            throw new HttpResponseException(response()->json([
                'status'  => false,
                'message' => 'Already enrolled in this course.',
            ], 422));
        }
        
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation error',
            'errors'  => $errors,
        ], 422));
    }
}
