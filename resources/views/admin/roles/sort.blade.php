@include('admin.crud.sort', [
    'formId'      => 'sort-roles-form',
    'saveUrl'     => route('admin.roles.sort.update'),
    'redirectUrl' => route('admin.roles.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'title',
        'subtitle' => 'designation',
        'extra'    => 'description'
    ]
])
