@include('admin.crud.form', [
    'action'     => route('admin.testimonials.update', $edit_data->id),
    'method'     => 'PUT',
    'formId'     => 'edit-testimonial-form',
    'submitText' => 'Update Testimonial',
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
            'value' => old('content', $edit_data->content),
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'user_name',
            'id' => 'user_name',
            'label' => 'User Name',
            'placeholder' => 'Enter User Name',
            'required' => true,
            'value' => old('user_name', $edit_data->user_name),
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'designation',
            'id' => 'designation',
            'label' => 'Designation',
            'placeholder' => 'Enter Designation',
            'value' => old('designation', $edit_data->designation),
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'profile_image',
            'label'     => 'Profile Image',
            'presetKey' => 'testimonials_profile_image',
            'circle'    => true,
            'value'     => $edit_data->profile_image_url,
        ],
        [
            'type' => 'number',
            'name' => 'rating',
            'id' => 'rating',
            'label' => 'Rating (1-5)',
            'placeholder' => 'Enter Rating',
            'min' => 1,
            'max' => 5,
            'value' => old('rating', $edit_data->rating),
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Published', 0 => 'Draft'],
            'value' => old('status', $edit_data->status),
            'col' => 6
        ],
    ]
])