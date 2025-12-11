@include('admin.crud.form', [
    'action' => route('admin.banners.store'),
    'formId' => 'add-banner-form',
    'submitText' => 'Save Banner',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.banners.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Banner Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Banner Image',
            'presetKey' => 'banners_image',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'action_type',
            'id' => 'action_type',
            'label' => 'Action Type',
            'required' => false,
            'options' => [
                'image' => 'Image',
                'video' => 'Video',
                'link' => 'Link',
                'mandara' => 'Mandara',
                'text' => 'Text'
            ],
            'col' => 12
        ],
        [
            'type' => 'url',
            'name' => 'action_value_url',
            'id' => 'action_value_url',
            'label' => 'Action Value',
            'placeholder' => 'Paste URL/Value Here.',
            'col' => 12,
            'show_if' => ['action_type' => ['video','link']]
        ],
        [
            'type' => 'text',
            'name' => 'action_value_mandara',
            'id' => 'action_value_mandara',
            'label' => 'Mandara Value',
            'placeholder' => 'Enter Mandara Value Here.',
            'col' => 12,
            'show_if' => ['action_type' => ['mandara']]
        ],
        [
            'type' => 'textarea',
            'name' => 'action_value_text',
            'id' => 'action_value_text',
            'label' => 'Content',
            'placeholder' => 'Provide content here.',
            'col' => 12,
            'show_if' => ['action_type' => ['text']]
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'col' => 12
        ],
    ]
])

