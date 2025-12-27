@include('admin.crud.form', [
    'action' => route('admin.estore-products.update', $edit_data->id),
    'formId' => 'edit-estore-product-form',
    'submitText' => 'Update Estore Product',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.estore-products.index'),
    'method'     => 'PUT',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Cottage Category Title',
            'required' => true,
            'value' => old('title', $edit_data->title),
            'col' => 12
        ],
        [
            'type'=>'select',
            'name'=>'category_id',
            'label'=>'Estore Category',
            'options'=>[''=>'Select Estore Category'] + $estoreCategories,
            'placeholder'=>'Select Estore Category',
            'required'=>true,
            'value' => old('category_id', $edit_data->category_id),
            'col'=>6
        ],
        [
            'type' => 'textarea',
            'name' => 'short_description',
            'id' => 'description',
            'label' => 'Short Description',
            'placeholder' => 'Enter Estore Product Short Description',
            'value' => old('short_description', $edit_data->short_description),
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Estore Product Description',
            'value' => old('description', $edit_data->description),
            'col' => 12
        ],
        [
            'type' => 'number',
            'name' => 'price',
            'id' => 'price',
            'label' => 'Price',
            'placeholder' => 'Enter Estore Product Price',
            'required' => true,
            'value' => old('price', $edit_data->price),
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'mrp',
            'id' => 'mrp',
            'label' => 'MRP',
            'placeholder' => 'Enter Estore Product MRP',
            'required' => true,
            'value' => old('mrp', $edit_data->mrp),
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'stock',
            'id' => 'stock',
            'label' => 'Stock',
            'placeholder' => 'Enter Estore Product Stock',
            'required' => true,
            'value' => old('stock', $edit_data->stock),
            'col' => 6
        ],
        [
            'type' => 'files',
            'name' => 'images',
            'label' => 'Images',
            'presetKey' => 'estore_products_images',
            'multiple' => true,
            'accept' => 'image/*',
            'value' => $edit_data->images_url,
            'required' => false,
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            'value' => old('status', $edit_data->status),
            'col' => 6
        ],
       
    ]
])