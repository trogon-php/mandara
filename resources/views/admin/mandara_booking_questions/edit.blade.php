@include('admin.crud.form', [
    'action' => route('admin.mandara-booking-questions.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-mandara-booking-questions-form',
    'submitText' => 'Update Mandara Booking Questions',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.mandara-booking-questions.index'),
    'fields' => [
        [
            'type'=>'textarea',
            'name'=>'question',
            'label'=>'Question',
            'value'=>old('question',$edit_data->question),
            'col'=>12
        ],
        [
            'type' => 'custom',
            'content' => view(
                "admin.mandara_booking_questions.fields.options",
                [
                    'name' => 'options',
                    'id' => 'options',
                    'options_data' => old('options', $edit_data->options ?? [])
                ]
            ),
            'col' => 12
        ],
       
        [
            'type' => 'hidden',
            'name' => 'require_remark',
            'value' => 0,
        ],
        [
            'type' => 'checkbox',
            'name' => 'require_remark',
            'label' => 'Require Remark',
            'value' => 1,
           'checked' => old('require_remark') !== null
            ? (bool) old('require_remark')
            : $edit_data->require_remark,
            'col' => 6
        ],
    ]
])