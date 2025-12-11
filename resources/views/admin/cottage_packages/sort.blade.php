@include('admin.crud.sort', [
    'formId'      => 'sort-packages-form',
    'saveUrl'     => route('admin.packages.sort.update'),
    'redirectUrl' => route('admin.packages.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => 'description',
        'extra'    => 'price'
    ]
])
