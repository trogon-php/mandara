@include('admin.crud.form', [
    'action' => route('admin.diet-plans.store'),
    'formId' => 'add-diet-plans-form',
    'submitText' => 'Save Diet Plans',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.diet-plans.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Diet Plan Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'slug',
            'name' => 'slug',
            'id' => 'slug',
            'label' => 'Slug',
            'placeholder' => 'Slug will be auto-generated from title',
            'required' => true,
            'related_field_id' => 'title',
            'model_name' => 'diet_plan',
            'col' => 12
        ],
        // [
        //     'type' => 'text',
        //     'name' => 'slug',
        //     'id' => 'slug',
        //     'label' => 'Slug',
        //     'placeholder' => 'Enter Diet Plan Slug (auto-generated from title if empty)',
        //     'required' => false,
        //     'col' => 12
        // ],
        [
            'type' => 'number',
            'name' => 'month',
            'id' => 'month',
            'label' => 'Month',
            'placeholder' => 'Enter Number of Month',
            'required' => true,
            'col' => 12
        ],
        [
            'type' => 'file',
            'name' => 'image',
            'label' => 'Diet Plan Image',
            'presetKey' => 'diet_plans_image',
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