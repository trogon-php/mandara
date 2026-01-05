@include('admin.crud.form', [
    'action' => route('admin.nurses.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-nurse-form',
    'submitText' => 'Update Nurse',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.nurses.index'),
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
            'type' => 'date',
            'name' => 'joining_date',
            'id' => 'joining_date',
            'label' => 'Joining Date',
            'placeholder' => 'Select Joining Date',
            'value' => $edit_data->joining_date ?? '',
            'col' => 6
        ],
        [
            'type' => 'qualification',
            'name' => 'qualification',
            'id' => 'qualification',
            'label' => 'Qualification',
            'placeholder' => 'Enter Qualification',
            'value' => $edit_data->qualification ?? '',
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'specialization',
            'id' => 'specialization',
            'label' => 'Specialization',
            'value' => $edit_data->specialization ?? '',
            'options' => [
                'maternity_care' => 'Maternity Care',
                'baby_care' => 'Baby Care',
            ],
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'seperator',
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'blood_group',
            'id' => 'blood_group',
            'label' => 'Blood Group',
            'placeholder' => 'Enter Blood Group',
            'required' => true,
            'value' => $edit_data->blood_group ?? '',
            'col' => 6
        ],
        [
            'type' => 'date',
            'name' => 'date_of_birth',
            'id' => 'date_of_birth',
            'label' => 'Date of Birth',
            'placeholder' => 'Select Date of Birth',
            'required' => true,
            'value' => $edit_data->date_of_birth ?? '',
            'col' => 6
        ]
    
    ]
])