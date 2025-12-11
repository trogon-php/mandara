@include('admin.crud.form', [
    'action' => route('admin.packages.store'),
    'formId' => 'add-package-form',
    'submitText' => 'Save Package',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.packages.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Package Title',
            'placeholder' => 'Enter Package Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Package Description',
            'col' => 12
        ],
        [
            'type' => 'number',
            'name' => 'price',
            'id' => 'price',
            'label' => 'Price',
            'placeholder' => 'Enter Package Price',
            'required' => true,
            'step' => '0.01',
            'min' => '0',
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'offer_price',
            'id' => 'offer_price',
            'label' => 'Offer Price (Optional)',
            'placeholder' => 'Enter Offer Price',
            'step' => '0.01',
            'min' => '0',
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'duration_days',
            'id' => 'duration_days',
            'label' => 'Duration (Days)',
            'placeholder' => 'Enter Duration in Days',
            'min' => '1',
            'col' => 6
        ],
        [
            'type' => 'date',
            'name' => 'expire_date',
            'id' => 'expire_date',
            'label' => 'Expire Date (Optional)',
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
            'col' => 12
        ]
    ]
])
