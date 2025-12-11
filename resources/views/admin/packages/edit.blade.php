@include('admin.crud.form', [
    'action' => route('admin.packages.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-package-form',
    'submitText' => 'Update Package',
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
            'value' => $edit_data->title,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Package Description',
            'value' => $edit_data->description,
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
            'value' => $edit_data->price,
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
            'value' => $edit_data->offer_price,
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'duration_days',
            'id' => 'duration_days',
            'label' => 'Duration (Days)',
            'placeholder' => 'Enter Duration in Days',
            'min' => '1',
            'value' => $edit_data->duration_days,
            'col' => 6
        ],
        [
            'type' => 'date',
            'name' => 'expire_date',
            'id' => 'expire_date',
            'label' => 'Expire Date (Optional)',
            'value' => $edit_data->expire_date ? $edit_data->expire_date->format('Y-m-d') : '',
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
            'value' => $edit_data->status,
            'col' => 12
        ]
    ]
])
