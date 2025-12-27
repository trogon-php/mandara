@include('admin.crud.sort', [
    'formId'      => 'sort-estore-categories-form',
    'saveUrl'     => route('admin.estore_categories.sort.update'),
    'redirectUrl' => route('admin.estore_categories.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => null,
        'extra'    => null
    ]
])