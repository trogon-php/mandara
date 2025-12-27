@include('admin.crud.form', [
    'action' => route('admin.estore-categories.update', $edit_data->id),
    'formId' => 'edit-estore-category-form',
    'submitText' => 'Update Estore Category',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.estore-categories.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Estore Category Title',
            'required' => true,
            'value' => old('title', $edit_data->title),
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Estore Category Description',
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
       
    ]
])