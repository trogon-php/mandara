@include('admin.crud.form', [
    'action' => route('admin.cottages.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-cottages-form',
    'submitText' => 'Update Cottages',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.cottages.index'),
    'fields' => [
        [
            'type'=>'text',
            'name'=>'title',
            'label'=>'Title',
            'value'=>old('title',$edit_data->title),
            'col'=>6
        ],
        [
            'type'=>'select',
            'name'=>'cottage_category_id',
            'label'=>'Cottage Category',
            'options'=>[''=>'Select Cottage Category'] + $cottageCategories,
            'placeholder'=>'Select Cottage Category',
            'required'=>true,
            'value'=>old('cottage_category_id',$edit_data->cottage_category_id),
            'col'=>6
        ],
        [
            'type'=>'textarea',
            'name'=>'short_description',
            'label'=>'Short Description',
            'placeholder'=>'Enter Cottage Short Description',
            'value'=>old('short_description',$edit_data->short_description),
            'col'=>12
        ],
        [
            'type'=>'textarea',
            'name'=>'description',
            'label'=>'Description',
            'placeholder'=>'Enter Cottage Description',
            'value'=>old('description',$edit_data->description),
            'col'=>12
        ],
        [
            'type'=>'files',
            'name'=>'images',
            'label'=>'Images',
            'presetKey' => 'cottages_image',
            'multiple' => true,
            'accept' => 'image/*',
            'value' => $edit_data->images_url,
            'col'=>12
        ],
        [
            'type' => 'number',
            'name' => 'capacity',
            'label'=>'Capacity',
            'placeholder'=>'Enter Cottage Capacity',
            'value'=>old('capacity',$edit_data->capacity),
            'col'=>4
        ],
        [
            'type' => 'number',
            'name' => 'bedrooms',
            'label'=>'Bedrooms',
            'placeholder'=>'Enter Cottage Bedrooms',
            'value'=>old('bedrooms',$edit_data->bedrooms),
            'col'=>4
        ],
        [
            'type' => 'number',
            'name' => 'bathrooms',
            'label'=>'Bathrooms',
            'placeholder'=>'Enter Cottage Bathrooms',
            'value'=>old('bathrooms',$edit_data->bathrooms),
            'col'=>4
        ],
        [
            'type'=>'select',
            'name'=>'status',
            'label'=>'Status',
            'options'=>['active'=>'Active','inactive'=>'Inactive'],
            'value'=>old('status',$edit_data->status),
            'col'=>6
        ]
    ]
])