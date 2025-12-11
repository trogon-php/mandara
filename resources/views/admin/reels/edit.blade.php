@include('admin.crud.form', [
    'action'     => route('admin.reels.update', $edit_data->id),
    'method'     => 'PUT',
    'formId'     => 'edit-reel-form',
    'submitText' => 'Update Reel',
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
            'value' => old('title', $edit_data->title),
            'col' => 6
        ],
        [
            'type' => 'select2',
            'name' => 'reel_category_id',
            'id' => 'reel_category_id',
            'label' => 'Reel Category',
            'options' => $reelCategories ?? [],
            'value' => old('reel_category_id', $edit_data->reel_category_id),
            'col' => 6
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Reel Description',
            'value' => old('description', $edit_data->description),
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
            'label' => 'Video [30 MB Max]',
            'placeholder' => 'Enter Video',
            'accept' => 'video/*',
            'value' => $edit_data->video_url,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'thumbnail',
            'label'     => 'Thumbnail',
            'presetKey' => 'reels_thumbnail',
            'value'     => $edit_data->thumbnail_url,
            'circle'    => true,
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
            'value' => old('premium', $edit_data->premium),
            'col' => 4
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => ['1' => 'Active', '0' => 'Inactive'],
            'value' => old('status', $edit_data->status),
            'col' => 4
        ],
        
    ]
])