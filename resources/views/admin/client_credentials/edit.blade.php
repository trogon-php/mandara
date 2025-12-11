@include('admin.crud.form', [
    'action' => route('admin.client-credentials.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-client-credential-form',
    'submitText' => 'Update Client Credential',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.client-credentials.index'),
    'fields' => [
        [
            'type' => 'select',
            'name' => 'provider',
            'id' => 'provider',
            'label' => 'Provider',
            'required' => true,
            'options' => $providerOptions,
            'value' => $edit_data->provider,
            'placeholder' => 'Select Provider',
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'required' => true,
            'value' => $edit_data->title,
            'placeholder' => 'Enter a friendly title for this credential',
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'credential_key',
            'id' => 'credential_key',
            'className' => 'filter-key-input',
            'label' => 'Credential Key',
            'required' => true,
            'value' => $edit_data->credential_key,
            'placeholder' => 'Enter unique key for programmatic access',
            'col' => 7,
            'disabled' => in_array($edit_data->credential_key, ['vimeo_primary', 'zoom_primary', '2factor_primary']),
            'help' => in_array($edit_data->credential_key, ['vimeo_primary', 'zoom_primary', '2factor_primary']) ? 'Primary credentials cannot be edited' : null
        ],
        [
            'type' => 'text',
            'name' => 'account_key',
            'id' => 'account_key',
            'className' => 'filter-whitespace-input',
            'label' => 'Account Key / API Key',
            // 'required' => true,
            'value' => $edit_data->decrypted_account_key,
            'placeholder' => 'Enter client ID or API key',
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'account_secret',
            'id' => 'account_secret',
            'className' => 'filter-whitespace-input',
            'label' => 'Account Secret / API Secret',
            // 'required' => true,
            'value' => $edit_data->decrypted_account_secret,
            'placeholder' => 'Enter client secret or API secret',
            'col' => 6
        ],
        [
            'type' => 'textarea',
            'name' => 'remarks',
            'id' => 'remarks',
            'label' => 'Remarks',
            'value' => $edit_data->decrypted_remarks,
            'placeholder' => 'Optional notes or usage information',
            'col' => 12
        ]
    ]
])
