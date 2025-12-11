@include('admin.crud.form', [
    'action' => route('admin.client-credentials.store'),
    'formId' => 'add-client-credential-form',
    'submitText' => 'Save Client Credential',
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
            'placeholder' => 'Select Provider',
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'required' => true,
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
            'placeholder' => 'Enter unique key for programmatic access',
            'col' => 7
        ],
        [
            'type' => 'text',
            'name' => 'account_key',
            'id' => 'account_key',
            'className' => 'filter-whitespace-input',
            'label' => 'Account Key / API Key',
            // 'required' => true,
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
            'placeholder' => 'Enter client secret or API secret',
            'col' => 6
        ],
        [
            'type' => 'textarea',
            'name' => 'remarks',
            'id' => 'remarks',
            'label' => 'Remarks',
            'placeholder' => 'Optional notes or usage information',
            'col' => 12
        ]
    ]
])
