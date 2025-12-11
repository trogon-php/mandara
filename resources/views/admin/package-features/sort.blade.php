@include('admin.crud.sort', [
    'formId'      => 'sort-package-features-form',
    'saveUrl'     => route('admin.package-features.sort.update'),
    'redirectUrl' => route('admin.package-features.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => 'description',
        'extra'    => null
    ]
])

