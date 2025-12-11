@include('admin.crud.form', [
    'action' => route('admin.cottage-categories.store'),
    'formId' => 'add-cottage-category-form',
    'submitText' => 'Save Cottage Category',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.cottage-categories.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Cottage Category Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Cottage Category Description',
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'col' => 6
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Thumbnail',
            'presetKey' => 'cottage_categories_thumbnail',
            'required' => false,
            'col' => 6
        ],
    ]
])