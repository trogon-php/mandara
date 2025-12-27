@include('admin.crud.form', [
    'action' => route('admin.estore-categories.store'),
    'formId' => 'add-estore-category-form',
    'submitText' => 'Save Estore Category',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.estore-categories.index'),
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
       
    ]
])