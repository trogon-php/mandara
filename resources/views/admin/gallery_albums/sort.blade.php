@include('admin.crud.sort', [
    'formId'      => 'sort-gallery-albums-form',
    'saveUrl'     => route('admin.gallery-albums.sort.update'),
    'redirectUrl' => route('admin.gallery-albums.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => 'description',
        'extra'    => null
    ]
])
