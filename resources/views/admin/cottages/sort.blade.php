@include('admin.crud.sort', [
    'formId' => 'sort-cottages-form',
    'saveUrl' => route('admin.cottages.sort.update'),
    'redirectUrl' => route('admin.cottages.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])