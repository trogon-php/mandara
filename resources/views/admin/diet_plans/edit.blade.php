@include('admin.crud.form', [
    'action' => route('admin.diet-plans.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-diet-plans-form',
    'submitText' => 'Update DietPlans',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.diet-plans.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Blog Title',
            'value' => old('title', $edit_data->title),
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'slug',
            'name' => 'slug',
            'id' => 'slug',
            'label' => 'Slug',
            'placeholder' => 'Enter Blog Slug (auto-generated from title if empty)',
            'value' => old('slug', $edit_data->slug),
            'required' => true,
            'related_field_id' => 'title',
            'model_name' => 'diet_plan',
            'exclude_id' => $edit_data->id,
            'col' => 12
        ],
        [
            'type' => 'number',
            'name' => 'month',
            'id' => 'month',
            'label' => 'Month',
            'placeholder' => 'Enter Number of Month',
            'value' => old('month', $edit_data->month),
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'image',
            'name' => 'image',
            'label' => 'Diet Plan Image',
            'presetKey' => 'diet_plans_image',
            'value' => old('image', $edit_data->image),
            'pasteable' => true,
            'required' => false,
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'short_description',
            'id' => 'short_description',
            'label' => 'Short Description',
            'placeholder' => 'Enter a short description of the diet plan',
            'value' => old('short_description', $edit_data->short_description),
            'required' => false,
            'col' => 12,
            'rows' => 3
        ],
        [
            'type' => 'textarea',
            'name' => 'content',
            'id' => 'content',
            'label' => 'Content',
            'placeholder' => 'Enter diet plan content',
            'value' => old('content', $edit_data->content),
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
            'value' => old('status', $edit_data->status),
            'col' => 12
        ],
    ]
])