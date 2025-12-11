<?php

namespace App\Http\Requests\Banners;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateBannerRequest extends BaseRequest
{
    /**
     * Normalize action_value_* into action_value
     */
    protected function prepareForValidation()
    {
        $actionType = $this->input('action_type');

        $actionValue = match ($actionType) {
            'video', 'link' => $this->input('action_value_url'),
            'text'          => $this->input('action_value_text'),
            'mandara'        => $this->input('action_value_mandara'),
            default         => null,
        };

        if ($actionType === 'text') {
            $this->merge([
                'description' => $actionValue,
                'action_value' => null,
            ]);
        }else{
            $this->merge([
                'action_value' => $actionValue,
            ]);
        }
    }

    /**
     * Strip out type-specific values after validation
     */
    public function validated($key = null, $default = null)
    {
        return collect(parent::validated())
            ->except(['action_value_url', 'action_value_text', 'action_value_mandara'])
            ->toArray();
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            'action_type' => [
                'sometimes',
                'nullable',
                Rule::in(['image', 'video', 'link', 'mandara', 'text'])
            ],

            // Variant rules
            'action_value_url' => 'nullable|required_if:action_type,video,link|url|max:255',
            'action_value_text' => 'nullable|required_if:action_type,text|string',
            'action_value_mandara' => 'nullable|required_if:action_type,mandara|string',

            // Final normalized field
            'action_value' => 'nullable|string|max:255',
            'description' => 'nullable|string',

            'status' => 'sometimes|required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Banner title is required',
            'title.max' => 'Banner title cannot exceed 255 characters',

            'action_value_url.required_if' => 'A valid URL is required when action type is Video or Link',
            'action_value_url.url' => 'Please provide a valid URL',

            'action_value_text.required_if' => 'Text content is required when action type is Text',

            'action_value_mandara.required_if' => 'Mandara value is required when action type is Mandara',
            'action_value_mandara.string' => 'Mandara value must be a string',

            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'image.max' => 'Image size cannot exceed 2MB',

            'status.required' => 'Status is required',
            'status.in' => 'Status must be either 0 (Inactive) or 1 (Active)',
        ];
    }
}
