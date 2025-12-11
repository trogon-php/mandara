@include('admin.crud.form', [
    'action'     => route('admin.notifications.update', $edit_data->id),
    'method'     => 'PUT',
    'formId'     => 'edit-notification-form',
    'submitText' => 'Update Notification',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.notifications.index'),
    'fields' => [
       [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Notification Title',
            'required' => true,
            'value' => old('title', $edit_data->title),
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Notification Description',
            'value' => old('description', $edit_data->description),
            'col' => 12
        ],
        [
            'type' => 'select2',
            'name' => 'course_id',
            'id' => 'course_id',
            'label' => 'Course',
            'options' => $courses ?? [],
            'value' => old('course_id', $edit_data->course_id),
            'col' => 6,
        ],
        [
            'type' => 'select2',
            'name' => 'category_id',
            'id' => 'category_id',
            'label' => 'Category',
            'options' => $categories ?? [],
            'value' => old('category_id', $edit_data->category_id),
            'col' => 6,
            'enabled' => has_feature('categories')
        ],
        [
            'type' => 'checkbox',
            'name' => 'premium',
            'id' => 'premium',
            'label' => 'Premium Notification',
            'value' => old('premium', $edit_data->premium),
            'defaultValue' => 0, // if unchecked case
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'action_link',
            'id' => 'action_link',
            'label' => 'Action Link',
            'placeholder' => 'Enter action link (optional)',
            'value' => old('action_link', $edit_data->action_link),
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label'     => 'Image',
            'presetKey' => 'notifications_image',
            'value'     => ($edit_data->image) ? $edit_data->image_url : '',
            'circle'    => true,
            'col' => 12
        ]
    ]
])
