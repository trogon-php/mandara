@include('admin.crud.form', [
    'action' => route('admin.gallery-images.store'),
    'formId' => 'add-gallery-image-form',
    'submitText' => 'Save Image',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.gallery-images.index'),
    'fields' => [
        [
            'type' => 'select2',
            'name' => 'album_id',
            'id' => 'album_id',
            'label' => 'Album',
            'placeholder' => 'Select Album',
            'options' => $albums,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Image Title',
            'placeholder' => 'Enter image title (optional)',
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter image description (optional)',
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Image File',
            'presetKey' => 'gallery_image',
            'required' => true,
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
