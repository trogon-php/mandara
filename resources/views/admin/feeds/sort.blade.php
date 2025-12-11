@include('admin.crud.sort', [
    'formId'      => 'sort-feeds-form',
    'saveUrl'     => route('admin.feeds.sort.update'),
    'redirectUrl' => route('admin.feeds.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => 'feed_type',
        'extra'    => null
    ]
])
