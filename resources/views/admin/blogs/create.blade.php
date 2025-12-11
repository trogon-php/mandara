@include('admin.crud.form', [
    'action' => route('admin.blogs.store'),
    'formId' => 'add-blog-form',
    'submitText' => 'Save Blog',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.blogs.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Blog Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'slug',
            'id' => 'slug',
            'label' => 'Slug',
            'placeholder' => 'Enter Blog Slug (auto-generated from title if empty)',
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Blog Image',
            'presetKey' => 'blogs_image',
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'short_description',
            'id' => 'short_description',
            'label' => 'Short Description',
            'placeholder' => 'Enter a brief description of the blog',
            'required' => false,
            'col' => 12,
            'rows' => 3
        ],
        [
            'type' => 'textarea',
            'name' => 'content',
            'id' => 'content',
            'label' => 'Content',
            'placeholder' => 'Enter blog content',
            'required' => true,
            'col' => 12,
            'rows' => 10
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