@include('admin.crud.form', [
    'action' => route('admin.gallery-albums.store'),
    'formId' => 'add-gallery-album-form',
    'submitText' => 'Save Album',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.gallery-albums.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Album Title',
            'placeholder' => 'Enter Album Title',
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
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Album Thumbnail',
            'presetKey' => 'gallery_thumbnail',
            'required' => false,
            'col' => 12
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
