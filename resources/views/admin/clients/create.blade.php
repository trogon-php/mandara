@include('admin.crud.form', [
    'action' => route('admin.clients.store'),
    'formId' => 'add-client-form',
    'submitText' => 'Save Client',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.clients.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'name',
            'id' => 'name',
            'label' => 'Full Name',
            'placeholder' => 'Enter Full Name',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'email',
            'id' => 'email',
            'label' => 'Email',
            'placeholder' => 'Enter Email Address',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'country-code',
            'name' => 'country_code',
            'id' => 'country_code',
            'label' => 'Country Code',
            'placeholder' => 'Select Country Code',
            'col' => 3
        ],
        [
            'type' => 'text',
            'name' => 'phone',
            'id' => 'phone',
            'label' => 'Phone Number',
            'placeholder' => 'Enter Phone Number',
            'col' => 3
        ],
        [
            'type' => 'seperator',
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'password',
            'id' => 'password',
            'label' => 'Password',
            'placeholder' => 'Enter Password (min 8 characters)',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [
                'active' => 'Active',
                'pending' => 'Pending',
                'blocked' => 'Blocked'
            ],
            'col' => 6
        ],
        [
            'type' => 'seperator',
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'profile_picture',
            'label' => 'Profile Picture',
            'presetKey' => 'profile_picture',
            'col' => 12
        ],
    ]
])