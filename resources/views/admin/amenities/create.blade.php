@include('admin.crud.form', [
    'action' => route('admin.amenities.store'),
    'formId' => 'add-amenity-form',
    'submitText' => 'Save Amenity',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.amenities.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Amenity Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type'=>'file',
            'name'=>'icon',
            'label'=>'Icon',
            'presetKey' => 'amenities_icon',
            'single' => true,
            'required'=>true,
            'col'=>6
        ],
        [
            'type'=>'textarea',
            'name'=>'description',
            'label'=>'Description',
            'placeholder'=>'Enter Amenity Description',
            'required'=>false,
            'col'=>12
        ],

        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'col' => 6
        ],
        [
            'type'  => 'repeater',
            'name'  => 'options',
            'label' => 'Options',
            'placeholder' => 'Enter Option',
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
                // [
                //     'type' => 'text',
                //     'name' => 'duration_text',
                //     'label' => 'Duration Text',
                //     'placeholder' => 'Enter Duration Text',
                // ],
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
                    'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
                    'required' => true,
                ],
            ]
        ],
       
    ]
])