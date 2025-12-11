@include('admin.crud.form', [
    'action' => route('admin.reel-categories.store'),
    'formId' => 'add-reel-category-form',
    'submitText' => 'Save Reel Category',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.reel-categories.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Reel Category Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => ['1' => 'Active', '0' => 'Inactive'],
            'col' => 6
        ]
    ]
])