@include('admin.crud.form', [
    'action' => route('admin.package-items.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-package-item-form',
    'submitText' => 'Update Package Item',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.package-items.index'),
    'fields' => [
        [
            'type' => 'select2',
            'name' => 'package_id',
            'id' => 'package_id',
            'label' => 'Package',
            'placeholder' => 'Select Package',
            'required' => true,
            'options' => $packages->pluck('title', 'id')->toArray(),
            'value' => old('package_id', $edit_data->package_id),
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
            'options' => $courses->pluck('title', 'id')->toArray(),
            'value' => old('item_id', $edit_data->item_id),
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
            'value' => old('status', $edit_data->status),
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'item_title',
            'id' => 'item_title',
            'label' => 'Item Title',
            'placeholder' => 'Custom title for this item (optional)',
            'value' => old('item_title', $edit_data->item_title),
            'col' => 6
        ]
    ]
])
