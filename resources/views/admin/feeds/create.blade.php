@include('admin.crud.form', [
    'action' => route('admin.feeds.store'),
    'formId' => 'add-feed-form',
    'submitText' => 'Save Feed',
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
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'content',
            'id' => 'content',
            'label' => 'Content',
            'placeholder' => 'Enter Feed Content',
            'col' => 12
        ],
        [
            'type' => 'select',
            'name' => 'feed_category_id',
            'id' => 'feed_category_id',
            'label' => 'Category',
            'options' => $categories ?? [],
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [1 => 'Active', 0 => 'Inactive'],
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
            'col' => 12
        ],
        // [
        //     'type' => 'file',
        //     'name' => 'feed_video',
        //     'id' => 'feed_video',
        //     'label' => 'Feed Video',
        //     'presetKey' => 'feeds_video',
        //     'accept' => 'video/*',
        //     'col' => 12
        // ],
    ]
])
