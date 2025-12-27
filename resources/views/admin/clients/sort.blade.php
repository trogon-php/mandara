@include('admin.crud.sort', [
    'formId' => 'sort-clients-form',
    'saveUrl' => route('admin.clients.sort.update'),
    'redirectUrl' => route('admin.clients.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])