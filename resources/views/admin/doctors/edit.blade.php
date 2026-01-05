@include('admin.crud.form', [
    'action' => route('admin.doctors.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-doctor-form',
    'submitText' => 'Update Doctor',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.doctors.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'name',
            'id' => 'name',
            'label' => 'Full Name',
            'placeholder' => 'Enter Full Name',
            'value' => $edit_data->name ?? '',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'email',
            'id' => 'email',
            'label' => 'Email',
            'placeholder' => 'Enter Email Address',
            'value' => $edit_data->email ?? '',
            'required' => true,
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
            'value' => $edit_data->status ?? 'active',
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
            'value' => $edit_data->profile_picture_url ?? '',
            'col' => 12
        ],

        [
            'type' => 'designation',
            'name' => 'designation',
            'id' => 'designation',
            'label' => 'Designation',
            'placeholder' => 'Enter Designation',
            'value' => $edit_data->designation ?? '',
            'col' => 6
        ],
    
    ]
])