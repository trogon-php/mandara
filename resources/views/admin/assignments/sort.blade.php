@include('admin.crud.sort', [
    'formId' => 'sort-assignments-form',
    'saveUrl' => route('admin.assignments.sort.update'),
    'redirectUrl' => route('admin.assignments.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])