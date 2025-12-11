@include('admin.crud.form', [
    'action' => route('admin.notifications.store'),
    'formId' => 'add-notification-form',
    'submitText' => 'Save Notification',
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
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Notification Description',
            'col' => 12
        ],
        [
            'type' => 'select2',
            'name' => 'course_id',
            'id' => 'course_id',
            'label' => 'Course',
            'options' => $courses ?? [],
            'col' => 6
        ],
        [
            'type' => 'select2',
            'name' => 'category_id',
            'id' => 'category_id',
            'label' => 'Category',
            'options' => $categories ?? [],
            'col' => 6,
            'enabled' => has_feature('categories')
        ],
        [
            'type' => 'checkbox',
            'name' => 'premium',
            'id' => 'premium-form',
            'label' => 'Premium Notification',
            'col' => 3
        ],
        [
            'type' => 'checkbox',
            'name' => 'free',
            'id' => 'free-form',
            'label' => 'Free Notification',
            'col' => 3
        ],
        [
            'type' => 'text',
            'name' => 'action_link',
            'id' => 'action_link',
            'label' => 'Action Link',
            'placeholder' => 'Enter action link (optional)',
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Image',
            'presetKey' => 'notifications_image',
            'subPreset' => 'original',
            'col' => 12
        ]
    ]
])
