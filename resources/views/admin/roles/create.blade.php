@include('admin.crud.form', [
    'action' => route('admin.roles.store'),
    'formId' => 'add-role-form',
    'submitText' => 'Save Role',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.roles.index'),
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
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Description',
            'required' => true,
            'col' => 12
        ],
          [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Published', 0 => 'Draft'],
            'col' => 12
        ],
    ]
])
