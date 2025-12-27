@include('admin.crud.form', [
    'action' => route('admin.meal-packages.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-meal-packages-form',
    'submitText' => 'Update Meal Package',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.meal-packages.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Meal Package Title',
            'value' => old('title', $edit_data->title),
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Thumbnail',
            'presetKey' => 'meal_packages_thumbnail',
            'value' => old('thumbnail', $edit_data->thumbnail_url),
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
            'value' => old('short_description', $edit_data->short_description),
            'required' => false,
            'col' => 12,
            'rows' => 3
        ],
        [
            'type' => 'textarea',
            'name' => 'content',
            'id' => 'content',
            'label' => 'Content',
            'placeholder' => 'Enter meal package content',
            'value' => old('content', $edit_data->content),
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
            'value' => old('labels', $edit_data->labels),
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
            'value' => old('is_veg', $edit_data->is_veg),
            'col' => 4
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'value' => old('status', $edit_data->status),
            'col' => 4
        ],
    ]
])