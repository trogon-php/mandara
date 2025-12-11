@include('admin.crud.form', [
    'action' => route('admin.gallery-albums.update', $edit_data->id),
    'formId' => 'edit-gallery-album-form',
    'submitText' => 'Update Album',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.gallery-albums.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Album Title',
            'placeholder' => 'Enter Album Title',
            'value' => $edit_data->title,
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter album description (optional)',
            'required' => false,
            'value' => $edit_data->description,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Album Thumbnail',
            'presetKey' => 'gallery_thumbnail',
            'required' => false,
            'value' => $edit_data->thumbnail_url,
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'value' => $edit_data->status,
            'col' => 12
        ],
    ]
])
