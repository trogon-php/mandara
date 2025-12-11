@include('admin.crud.sort', [
    'formId'      => 'sort-package-items-form',
    'saveUrl'     => route('admin.package-items.sort.update'),
    'redirectUrl' => route('admin.package-items.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'item_title',
        'subtitle' => 'item_type',
        'extra'    => 'package.title'
    ]
])