@include('admin.crud.form', [
    'action' => route('admin.blogs.update', $edit_data->id),
    'formId' => 'edit-blog-form',
    'submitText' => 'Update Blog',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.blogs.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Blog Title',
            'required' => true,
            'value' => $edit_data->title,
            'col' => 12
        ],
        [
            'type' => 'text',
            'name' => 'slug',
            'id' => 'slug',
            'label' => 'Slug',
            'placeholder' => 'Enter Blog Slug',
            'required' => false,
            'value' => $edit_data->slug,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Blog Image',
            'presetKey' => 'blogs_image',
            'value' => $edit_data->image_url,
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'short_description',
            'id' => 'short_description',
            'label' => 'Short Description',
            'placeholder' => 'Enter a brief description of the blog',
            'value' => $edit_data->short_description,
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
            'value' => $edit_data->content,
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
            'value' => $edit_data->status,
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'col' => 12
        ],
    ]
])