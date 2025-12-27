@include('admin.crud.form', [
    'action' => route('admin.meal-packages.store'),
    'formId' => 'add-meal-packages-form',
    'submitText' => 'Save Meal Package',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.meal-packages.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Thumbnail',
            'presetKey' => 'meal_packages_thumbnail',
            'pasteable' => true,
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'short_description',
            'id' => 'short_description',
            'label' => 'Short Description',
            'placeholder' => 'Enter a short description',
            'required' => false,
            'col' => 12,
            'rows' => 3
        ],
        [
            'type' => 'textarea',
            'name' => 'content',
            'id' => 'content',
            'label' => 'Content',
            'placeholder' => 'Enter content',
            'required' => false,
            'col' => 12,
            'rows' => 10
        ],
        [
            'type' => 'tags',
            'name' => 'labels',
            'id' => 'labels',
            'label' => 'Labels',
            'placeholder' => 'Enter labels (comma separated)',
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'is_veg',
            'id' => 'is_veg',
            'label' => 'Type',
            'required' => true,
            'options' => [1 => 'Vegetarian', 0 => 'Non-Vegetarian'],
            'col' => 4
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'col' => 4
        ],
    ]
])