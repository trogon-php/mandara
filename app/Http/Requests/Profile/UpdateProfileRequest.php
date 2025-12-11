<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User is already authenticated via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'country_code' => 'sometimes|string|max:5',
            'phone' => 'sometimes|string|max:20',
            'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'country_code.string' => 'Country code must be a valid string.',
            'country_code.max' => 'Country code cannot exceed 5 characters.',
            'phone.string' => 'Phone must be a valid string.',
            'phone.max' => 'Phone cannot exceed 20 characters.',
            'profile_picture.image' => 'Profile picture must be a valid image file.',
            'profile_picture.mimes' => 'Profile picture must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'profile_picture.max' => 'Profile picture cannot exceed 2MB.',
        ];
    }
}
