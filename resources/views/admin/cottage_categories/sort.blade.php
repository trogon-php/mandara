@include('admin.crud.sort', [
    'formId'      => 'sort-cottage-categories-form',
    'saveUrl'     => route('admin.cottage_categories.sort.update'),
    'redirectUrl' => route('admin.cottage_categories.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => null,
        'extra'    => null
    ],
    'previewImageKey' => 'thumbnail'
])