@include('admin.crud.form', [
    'action' => route('admin.package-features.store'),
    'formId' => 'add-package-feature-form',
    'submitText' => 'Save Package Feature',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.package-features.index'),
    'fields' => [
        [
            'type' => 'select',
            'name' => 'package_id',
            'id' => 'package_id',
            'label' => 'Package',
            'required' => true,
            'options' => $packages,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Feature Title',
            'placeholder' => 'Enter Feature Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Feature Description',
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
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'sort_order',
            'id' => 'sort_order',
            'label' => 'Sort Order',
            'placeholder' => 'Enter Sort Order',
            'min' => '0',
            'col' => 6
        ]
    ]
])

