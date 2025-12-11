@include('admin.crud.form', [
    'action'     => route('admin.feeds.update', $edit_data->id),
    'method'     => 'PUT',
    'formId'     => 'edit-feed-form',
    'submitText' => 'Update Feed',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.feeds.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Feed Title',
            'required' => true,
            'value' => old('title', $edit_data->title),
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'content',
            'id' => 'content',
            'label' => 'Content',
            'placeholder' => 'Enter Feed Content',
            'value' => old('content', $edit_data->content),
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'feed_category_id',
            'id' => 'feed_category_id',
            'label' => 'Category',
            'options' => ['' => 'Select Category'] + $categories->pluck('title', 'id')->toArray(),
            'value' => old('feed_category_id', $edit_data->feed_category_id),
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'value' => old('status', $edit_data->status),
            'col' => 6
        ],
        [
            'type' => 'files',
            'name' => 'feed_image',
            'id' => 'feed_image',
            'label' => 'Feed Images',
            'presetKey' => 'feeds_image',
            'multiple' => true,
            'accept' => 'image/*',
            'value' => $edit_data->feed_image_url,
            'col' => 12
        ],
        [
            'type' => 'file',
            'name' => 'feed_video',
            'id' => 'feed_video',
            'label' => 'Feed Video',
            'presetKey' => 'feeds_video',
            'accept' => 'video/*',
            'value' => $edit_data->feed_video_url,
            'col' => 12
        ],
    ]
])
