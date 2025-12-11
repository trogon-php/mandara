@include('admin.crud.sort', [
    'formId'      => 'sort-reels-form',
    'saveUrl'     => route('admin.reels.sort.update'),
    'redirectUrl' => route('admin.reels.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => null,
        'extra'    => null
    ]
])
