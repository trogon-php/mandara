@include('admin.crud.sort', [
    'formId'      => 'sort-notifications-form',
    'saveUrl'     => route('admin.notifications.sort.update'),
    'redirectUrl' => route('admin.notifications.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'user_name',
        'subtitle' => 'designation',
        'extra'    => 'content'
    ]
])
