@include('admin.crud.form', [
    'action' => route('admin.package-items.store'),
    'formId' => 'add-package-item-form',
    'submitText' => 'Save Package Item',
    'class' => 'ajax-crud-form',
    'redirect' => isset($packageId) ? route('admin.packages.index') : route('admin.package-items.index'),
    'fields' => [
        [
            'type' => 'select2',
            'name' => 'package_id',
            'id' => 'package_id',
            'label' => 'Package',
            'placeholder' => 'Select Package',
            'required' => true,
            'options' => $packagesOptions,
            'value' => $packageId ?? null,
            'col' => 6
        ],
        [
            'type' => 'hidden',
            'name' => 'item_type',
            'id' => 'item_type',
            'value' => 'course'
        ],
        [
            'type' => 'select2',
            'name' => 'item_id',
            'id' => 'item_id',
            'label' => 'Course',
            'placeholder' => 'Select Course',
            'required' => true,
            'options' => $coursesOptions,
            'col' => 6
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
            'type' => 'text',
            'name' => 'item_title',
            'id' => 'item_title',
            'label' => 'Item Title',
            'placeholder' => 'Custom title for this item (optional)',
            'col' => 6
        ]
    ]
])
