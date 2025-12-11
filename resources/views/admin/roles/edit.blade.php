@include('admin.crud.form', [
    'action'     => route('admin.roles.update', $edit_data->id),
    'method'     => 'PUT',
    'formId'     => 'edit-role-form',
    'submitText' => 'Update Role',
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
            'value' => old('title', $edit_data->title),
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Description',
            'required' => true,
            'value' => old('description', $edit_data->description),
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Published', 0 => 'Draft'],
            'value' => old('status', $edit_data->status),
            'col' => 12
        ],
    ]
])
