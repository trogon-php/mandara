@include('admin.crud.form', [
    'action' => route('admin.estore-products.store'),
    'formId' => 'add-estore-product-form',
    'submitText' => 'Save Estore Product',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.estore-products.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter Cottage Category Title',
            'required' => true,
            'col' => 12
        ],
        [
            'type'=>'select',
            'name'=>'category_id',
            'label'=>'Estore Category',
            'options'=>[''=>'Select Estore Category'] + $estoreCategories,
            'placeholder'=>'Select Estore Category',
            'required'=>true,
            'col'=>6
        ],
        [
            'type' => 'textarea',
            'name' => 'short_description',
            'id' => 'description',
            'label' => 'Short Description',
            'placeholder' => 'Enter Estore Product Short Description',
            'col' => 12
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter Estore Product Description',
            'col' => 12
        ],
        [
            'type' => 'number',
            'name' => 'price',
            'id' => 'price',
            'label' => 'Price',
            'placeholder' => 'Enter Estore Product Price',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'mrp',
            'id' => 'mrp',
            'label' => 'MRP',
            'placeholder' => 'Enter Estore Product MRP',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'stock',
            'id' => 'stock',
            'label' => 'Stock',
            'placeholder' => 'Enter Estore Product Stock',
            'required' => true,
            'col' => 6
        ],
        [
            'type' => 'files',
            'name' => 'images',
            'label' => 'Images',
            'presetKey' => 'estore_products_images',
            'multiple' => true,
            'accept' => 'image/*',
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
            'col' => 6
        ],
       
    ]
])