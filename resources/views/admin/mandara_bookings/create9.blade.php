@include('admin.crud.form', [
    'action' => route('admin.mandara-bookings.store'),
    'formId' => 'add-mandara-booking-form',
    'submitText' => 'Reserve My Stay',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.mandara-bookings.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'email',
            'id' => 'email',
            'label' => 'Email',
            'placeholder' => 'Enter the email of the client',
            'required' => false,
            'col' => 12
        ],
        [
            'type'=>'text',
            'name'=>'phone',
            'label'=>'Phone',
            'placeholder'=>'Enter the phone number of the client',
            'required'=>false,
            'col'=>12
        ],
        [
            'type' => 'select',
            'name' => 'is_delivered',
            'id' => 'is_delivered',
            'label' => 'Have you delivered your baby?',
            'placeholder' => 'Select the delivery status',
            'options' => [
                1 => 'Delivered',
                0 => 'Expected',
            ],
            'col' => 12
        ],
        [
            'type' => 'date',
            'name' => 'delivery_date',
            'id' => 'delivery_date',
            'label' => 'Delivery Date',
            'placeholder' => 'Select the delivery date',
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'date',
            'name' => 'date_from',
            'id' => 'date_from',
            'label' => 'Arrival Date',
            'placeholder' => 'Select the arrival date',
            'required' => false,
            'col' => 6
        ],
        [
            'type' => 'date',
            'name' => 'date_to',
            'id' => 'date_to',
            'label' => 'Departure Date',
            'placeholder' => 'Select the departure date',
            'required' => false,
            'col' => 6
        ],
        [
            'type' => 'textarea',
            'name' => 'stock',
            'id' => 'additional_note',
            'label' => 'Additional Note',
            'placeholder' => 'Enter the additional note',
            'required' => false,
            'col' => 6
        ],
       
    ]
])