<?php

namespace App\Http\Requests\ClientCredentials;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateClientCredentialRequest extends BaseRequest
{
    public function rules(): array
    {
        $clientCredentialId = $this->route('client_credential');
        
        return [
            'provider' => 'required|in:vimeo,zoom,2factor',
            'title' => 'required|string|max:255',
            'credential_key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('client_credentials', 'credential_key')->ignore($clientCredentialId)
            ],
            'account_key' => 'required|string|max:255',
            'account_secret' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'provider.required' => 'Please select a provider.',
            'provider.in' => 'Please select a valid provider.',
            'title.required' => 'Please enter a title.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'credential_key.required' => 'Please enter a credential key.',
            'credential_key.max' => 'Credential key cannot exceed 100 characters.',
            'credential_key.unique' => 'This credential key is already in use.',
            'account_key.required' => 'Please enter an account key.',
            'account_key.max' => 'Account key cannot exceed 255 characters.',
            'account_secret.required' => 'Please enter an account secret.',
            'account_secret.max' => 'Account secret cannot exceed 255 characters.',
            'remarks.max' => 'Remarks cannot exceed 255 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'provider' => 'Provider',
            'title' => 'Title',
            'credential_key' => 'Credential Key',
            'account_key' => 'Account Key',
            'account_secret' => 'Account Secret',
            'remarks' => 'Remarks',
        ];
    }
}

