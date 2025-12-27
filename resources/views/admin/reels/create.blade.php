@include('admin.crud.form', [
    'action' => route('admin.reels.store'),
    'formId' => 'add-reel-form',
    'submitText' => 'Save Reel',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.reels.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Reel Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'select2',
            'name' => 'reel_category_id',
            'id' => 'reel_category_id',
            'label' => 'Reel Category',
            'required' => true,
            'options' => $reelCategories ?? [],
            'col' => 6
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Reel Description',
            'col' => 12
        ],
        [
            'type' => 'seperator',
            'col' => 12
        ],
        [
            'type' => 'file',
            'name' => 'video',
            'id' => 'video',
            'label' => 'Upload Video [30 MB Max]',
            'placeholder' => 'Upload Video [30 MB Max]',
            'accept' => 'video/*',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label' => 'Thumbnail',
            'presetKey' => 'reels_thumbnail',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'seperator',
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'premium',
            'id' => 'premium',
            'label' => 'Content Type',
            'required' => true,
            'options' => ['0' => 'Free', '1' => 'Premium'],
            'col' => 4
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => ['1' => 'Active', '0' => 'Inactive'],
            'col' => 4
        ],
    ]
])