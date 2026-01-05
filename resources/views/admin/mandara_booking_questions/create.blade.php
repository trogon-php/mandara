@include('admin.crud.form', [
    'action' => route('admin.mandara-booking-questions.store'),
    'formId' => 'add-mandara-booking-questions-form',
    'submitText' => 'Save Mandara Booking Questions',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.mandara-booking-questions.index'),
    'fields' => [
        [
            'type'=>'textarea',
            'name'=>'question',
            'label'=>'Question',
            'placeholder'=>'Enter Mandara Booking Question',
            'required'=>true,
            'col'=>12
        ],
        [
            'type'=>'custom',
            'content'=>view("admin.mandara_booking_questions.fields.options",['name' => 'options']),
            'col'=>12
],

        // [
        //     'type'  => 'repeater',
        //     'name'  => 'options',
        //     'label' => 'Options',
        //     'placeholder' => 'Enter Option',
        //     'required' => true,
        //     'col' => 12,
        //     'fields' => [
        //         [
        //             'type' => 'text',
        //             'name' => 'option',
        //             'label' => 'Option Name',
        //             'placeholder' => 'Enter Option',
        //         ]
        //     ]
        // ],
        [
            'type'=>'checkbox',
            'name'=>'require_remark',
            'label'=>'Require Remark',
            'defaultValue'=>0,
            'col'=>6
        ],
       
    ]
])