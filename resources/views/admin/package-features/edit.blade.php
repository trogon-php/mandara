@include('admin.crud.form', [
    'action' => route('admin.package-features.update', $edit_data->id),
    'formId' => 'edit-package-feature-form',
    'submitText' => 'Update Package Feature',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.package-features.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'select',
            'name' => 'package_id',
            'id' => 'package_id',
            'label' => 'Package',
            'required' => true,
            'options' => $packages,
            'value' => $edit_data->package_id,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Feature Title',
            'placeholder' => 'Enter Feature Title',
            'required' => true,
            'value' => $edit_data->title,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Feature Description',
            'value' => $edit_data->description,
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive'
            ],
            'value' => $edit_data->status,
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'sort_order',
            'id' => 'sort_order',
            'label' => 'Sort Order',
            'placeholder' => 'Enter Sort Order',
            'min' => '0',
            'value' => $edit_data->sort_order,
            'col' => 6
        ]
    ]
])

