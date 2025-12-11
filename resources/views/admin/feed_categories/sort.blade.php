@include('admin.crud.sort', [
    'formId'      => 'sort-feed-categories-form',
    'saveUrl'     => route('admin.feed-categories.sort.update'),
    'redirectUrl' => route('admin.feed-categories.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => null,
        'extra'    => null
    ]
])
