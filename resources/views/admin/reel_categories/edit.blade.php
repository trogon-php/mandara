@include('admin.crud.form', [
    'action'     => route('admin.reel-categories.update', $edit_data->id),
    'method'     => 'PUT',
    'formId'     => 'edit-reel-category-form',
    'submitText' => 'Update Reel Category',
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
            'value' => old('title', $edit_data->title),
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => ['1' => 'Active', '0' => 'Inactive'],
            'value' => old('status', $edit_data->status),
            'col' => 6
        ]
    ]
])