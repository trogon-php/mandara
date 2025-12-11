@include('admin.crud.form', [
    'action' => route('admin.gallery-images.update', $edit_data->id),
    'formId' => 'edit-gallery-image-form',
    'submitText' => 'Update Image',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.gallery-images.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'select2',
            'name' => 'album_id',
            'id' => 'album_id',
            'label' => 'Album',
            'placeholder' => 'Select Album',
            'options' => $albums,
            'value' => $edit_data->album_id,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Image Title',
            'placeholder' => 'Enter image title (optional)',
            'required' => false,
            'value' => $edit_data->title,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter image description (optional)',
            'required' => false,
            'value' => $edit_data->description,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Image File',
            'presetKey' => 'gallery_image',
            'required' => false,
            'value' => $edit_data->image_url,
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
