@include('admin.crud.sort', [
    'formId'      => 'sort-reel-categories-form',
    'saveUrl'     => route('admin.reel-categories.sort.update'),
    'redirectUrl' => route('admin.reel-categories.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => null,
        'extra'    => null
    ]
])
