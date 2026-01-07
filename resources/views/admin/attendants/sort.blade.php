@include('admin.crud.sort', [
    'formId' => 'sort-attendants-form',
    'saveUrl' => route('admin.attendants.sort.update'),
    'redirectUrl' => route('admin.attendants.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])