@include('admin.crud.form', [
    'action' => route('admin.amenities.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-amenity-form',
    'submitText' => 'Update Amenity',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.amenities.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Amenity Title',
            'value' => old('title', $edit_data->title),
            'required' => true,
            'col' => 12
        ],
        [
            'type'=>'image',
            'name'=>'icon',
            'label'=>'Icon',
            'presetKey' => 'amenities_icon',
            'single' => true,
            'value' => $edit_data->icon_url,
            'required'=>false,
            'col'=>6
        ],
        [
            'type'=>'textarea',
            'name'=>'description',
            'label'=>'Description',
            'placeholder'=>'Enter Amenity Description',
            'value' => old('description', $edit_data->description),
            'required'=>false,
            'col'=>12
        ],

        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'value' => old('status', $edit_data->status),
            'required' => true,
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'col' => 6
        ],
        [
            'type'  => 'repeater',
            'name'  => 'options',
            'label' => 'Options',
            'value' => old('options', $edit_data->items),
            'required' => true,
            'col' => 12,
            'fields' => [
                [
                    'type' => 'text',
                    'name' => 'title',
                    'label' => 'Title',
                    'placeholder' => 'Enter Title',
                ],
                [
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => 'Description',
                    'placeholder' => 'Enter Description',
                ],
                [
                    'type' => 'number',
                    'name' => 'duration_minutes',
                    'label' => 'Duration Minutes',
                    'placeholder' => 'Enter Duration Minutes',
                ],
                [
                    'type' => 'text',
                    'name' => 'duration_text',
                    'label' => 'Duration Text',
                    'placeholder' => 'Enter Duration Text',
                ],
                [
                    'type' => 'number',
                    'name' => 'price',
                    'label' => 'Price',
                    'placeholder' => 'Enter Price',
                ],
                [
                    'type' => 'select',
                    'name' => 'status',
                    'label' => 'Status',
                    'options' => [
                        'active' => 'Active',
                        'inactive' => 'Inactive'
                    ],
                ],
            ]
        ]

       
    ]
])