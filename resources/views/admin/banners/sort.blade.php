@include('admin.crud.sort', [
    'formId'      => 'sort-banners-form',
    'saveUrl'     => route('admin.banners.sort.update'),
    'redirectUrl' => route('admin.banners.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => 'action_type',
        'extra'    => null
    ]
])

