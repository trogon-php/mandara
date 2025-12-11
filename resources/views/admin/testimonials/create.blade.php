@include('admin.crud.form', [
    'action' => route('admin.testimonials.store'),
    'formId' => 'add-testimonial-form',
    'submitText' => 'Save Testimonial',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.testimonials.index'),
    'fields' => [
        [
            'type' => 'textarea',
            'name' => 'content',
            'id' => 'content',
            'label' => 'Content',
            'placeholder' => 'Enter Testimonial Content',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'user_name',
            'id' => 'user_name',
            'label' => 'User Name',
            'placeholder' => 'Enter User Name',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'designation',
            'id' => 'designation',
            'label' => 'Designation',
            'placeholder' => 'Enter Designation',
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'profile_image',
            'label' => 'Profile Image',
            'presetKey' => 'testimonials_profile_image',
            'circle' => true
        ],
        [
            'type' => 'number',
            'name' => 'rating',
            'id' => 'rating',
            'label' => 'Rating',
            'placeholder' => 'Enter Rating (1-5)',
            'min' => 1,
            'max' => 5,
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Published', 0 => 'Draft'],
            'col' => 6
        ],
    ]
])