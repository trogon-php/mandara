@include('admin.crud.form', [
    'action' => route('admin.cottage-categories.update', $edit_data->id),
    'formId' => 'edit-cottage-category-form',
    'submitText' => 'Update Cottage Category',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.cottage-categories.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Cottage Category Title',
            'required' => true,
            'value' => old('title', $edit_data->title),
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Cottage Category Description',
            'value' => old('description', $edit_data->description),
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'value' => old('status', $edit_data->status),
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Thumbnail',
            'presetKey' => 'cottage_categories_thumbnail',
            'value' => $edit_data->thumbnail_url,
            'required' => false,
            'col' => 6
        ],
    ]
])