@include('admin.crud.sort', [
    'formId'      => 'sort-gallery-images-form',
    'saveUrl'     => route('admin.gallery-images.sort.update'),
    'redirectUrl' => route('admin.gallery-images.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => 'description',
        'extra'    => null
    ]
])
