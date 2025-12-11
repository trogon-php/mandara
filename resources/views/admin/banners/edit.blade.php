@include('admin.crud.form', [
    'action' => route('admin.banners.update', $edit_data->id),
    'formId' => 'edit-banner-form',
    'submitText' => 'Update Banner',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.banners.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Banner Title',
            'required' => true,
            'value' => $edit_data->title,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Banner Image',
            'presetKey' => 'banners_image',
            'value' => $edit_data->image_url,
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'action_type',
            'id' => 'action_type',
            'label' => 'Action Type',
            'required' => true,
            'value' => $edit_data->action_type,
            'options' => [
                'image' => 'Image',
                'video' => 'Video',
                'link' => 'Link',
                'mandara' => 'Mandara',
                'text' => 'Text'
            ],
            'col' => 12
        ],
        // Show when type = video or link
        [
            'type' => 'url',
            'name' => 'action_value_url',
            'id' => 'action_value_url',
            'label' => 'Action URL',
            'placeholder' => 'Paste Video/Link URL',
            'value' => in_array($edit_data->action_type, ['video','link']) ? $edit_data->action_value : null,
            'col' => 12,
            'show_if' => ['action_type' => ['video','link']]
        ],
        [
            'type' => 'text',
            'name' => 'action_value_mandara',
            'id' => 'action_value_mandara',
            'label' => 'Mandara Value',
            'placeholder' => 'Enter Mandara Value Here.',
            'value' => $edit_data->action_type === 'mandara' ? $edit_data->action_value : null,
            'col' => 12,
            'show_if' => ['action_type' => ['mandara']]
        ],
        // Show when type = text
        [
            'type' => 'textarea',
            'name' => 'action_value_text',
            'id' => 'action_value_text',
            'label' => 'Text Content',
            'placeholder' => 'Enter banner text',
            'value' => $edit_data->action_type === 'text' ? $edit_data->description : null,
            'col' => 12,
            'show_if' => ['action_type' => ['text']]
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'value' => $edit_data->status,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'col' => 12
        ],
    ]
])
