@include('admin.crud.sort', [
    'formId'      => 'sort-estore-products-form',
    'saveUrl'     => route('admin.estore_products.sort.update'),
    'redirectUrl' => route('admin.estore_products.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => null,
        'extra'    => null
    ]
])