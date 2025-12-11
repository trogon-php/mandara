@include('admin.crud.form', [
    'action' => route('admin.feed-categories.store'),
    'formId' => 'add-feed-category-form',
    'submitText' => 'Save Feed Category',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.feed-categories.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Category Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'col' => 12
        ],
    ]
])
