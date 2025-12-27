@include('admin.crud.form', [
    'action' => route('admin.clients.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-client-form',
    'submitText' => 'Update Client',
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
            'value' => $edit_data->name ?? '',
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'email',
            'id' => 'email',
            'label' => 'Email',
            'placeholder' => 'Enter Email Address',
            'required' => true,
            'value' => $edit_data->email ?? '',
            'col' => 6
        ],
        [
            'type' => 'country-code',
            'name' => 'country_code',
            'id' => 'country_code',
            'label' => 'Country Code',
            'placeholder' => 'Select Country Code',
            'value' => $edit_data->country_code ?? '',
            'col' => 3
        ],
        [
            'type' => 'text',
            'name' => 'phone',
            'id' => 'phone',
            'label' => 'Phone Number',
            'placeholder' => 'Enter Phone Number',
            'value' => $edit_data->phone ?? '',
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
            'placeholder' => 'Leave blank to keep current password',
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
            'value' => $edit_data->status ?? 'active',
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
            'value' => $edit_data->profile_picture_url ?? '',
            'col' => 12
        ],
    ]
])