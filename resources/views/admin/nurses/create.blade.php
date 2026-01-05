@include('admin.crud.form', [
    'action' => route('admin.nurses.store'),
    'formId' => 'add-nurse-form',
    'submitText' => 'Save Nurse',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.nurses.index'),
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
        [
            'type' => 'date',
            'name' => 'joining_date',
            'id' => 'joining_date',
            'label' => 'Joining Date',
            'placeholder' => 'Select Joining Date',
            'col' => 6
        ],
        [
            'type' => 'qualification',
            'name' => 'qualification',
            'id' => 'qualification',
            'label' => 'Qualification',
            'placeholder' => 'Enter Qualification',
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'specialization',
            'id' => 'specialization',
            'label' => 'Specialization',
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
            'col' => 6
        ],
        [
            'type' => 'date',
            'name' => 'date_of_birth',
            'id' => 'date_of_birth',
            'label' => 'Date of Birth',
            'placeholder' => 'Select Date of Birth',
            'required' => true,
            'col' => 6
        ]
    
    ]
])