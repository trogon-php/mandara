@include('admin.crud.form', [
    'action' => route('admin.cottages.store'),
    'formId' => 'add-cottages-form',
    'submitText' => 'Save Cottages',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.cottages.index'),
    'fields' => [
        [
            'type'=>'text',
            'name'=>'title',
            'label'=>'Title',
            'placeholder'=>'Enter Cottage Title',
            'required'=>true,
            'col'=>6
        ],
        [
            'type'=>'select',
            'name'=>'cottage_category_id',
            'label'=>'Cottage Category',
            'options'=>[''=>'Select Cottage Category'] + $cottageCategories,
            'placeholder'=>'Select Cottage Category',
            'required'=>true,
            'col'=>6
        ],
        [
            'type'=>'textarea',
            'name'=>'short_description',
            'label'=>'Short Description',
            'placeholder'=>'Enter Cottage Short Description',
            'required'=>false,
            'col'=>12
        ],
        [
            'type'=>'textarea',
            'name'=>'description',
            'label'=>'Description',
            'placeholder'=>'Enter Cottage Description',
            'required'=>false,
            'col'=>12
        ],
        [
            'type'=>'files',
            'name'=>'images',
            'label'=>'Images',
            'presetKey' => 'cottages_image',
            'multiple' => true,
            'accept' => 'image/*',
            'required'=>true,
            'col'=>12
        ],
        [
            'type' => 'number',
            'name' => 'capacity',
            'label'=>'Capacity',
            'placeholder'=>'Enter Cottage Capacity',
            'required'=>true,
            'col'=>4
        ],
        [
            'type' => 'number',
            'name' => 'bedrooms',
            'label'=>'Bedrooms',
            'placeholder'=>'Enter Cottage Bedrooms',
            'required'=>true,
            'col'=>4
        ],
        [
            'type' => 'number',
            'name' => 'bathrooms',
            'label'=>'Bathrooms',
            'placeholder'=>'Enter Cottage Bathrooms',
            'required'=>true,
            'col'=>4
        ],
        [
            'type'=>'select',
            'name'=>'status',
            'label'=>'Status',
            'options'=>['active'=>'Active','inactive'=>'Inactive'],
            'required'=>true,
            'col'=>6
        ]
    ]
])