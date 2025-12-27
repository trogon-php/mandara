@include('admin.crud.form', [
    'action' => route('admin.cottage-packages.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-cottage-package-form',
    'submitText' => 'Update Package',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.cottage-packages.index'),
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
            'type' => 'select',
            'name' => 'cottage_category_id',
            'id' => 'cottage_category_id',
            'label' => 'Cottage Category',
            'required' => true,
            'value' => $edit_data->cottage_category_id,
            'options' => [''=>'Select Cottage Category'] + $cottageCategories,
            'col' => 6
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
            'name' => 'discount_amount',
            'id' => 'discount_amount',
            'label' => 'Discount Amount (Optional)',
            'placeholder' => 'Enter Discount Amount',
            'step' => '0.01',
            'min' => '0',
            'value' => $edit_data->discount_amount,
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
            'type' => 'number',
            'name' => 'booking_amount',
            'id' => 'booking_amount',
            'label' => 'Booking Amount',
            'placeholder' => 'Enter Booking Amount',
            'value' => $edit_data->booking_amount,
            'min' => '0',
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
            'col' => 6
        ],
        [
            'type' => 'checkbox',
            'name' => 'tax_included',
            'id' => 'tax_included',
            'label' => 'Tax Included..?',
            'value' => $edit_data->tax_included,
            'defaultValue' => 0,
            'col' => 6
        ],
    ]
])
