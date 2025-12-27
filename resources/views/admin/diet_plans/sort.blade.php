@include('admin.crud.sort', [
    'formId' => 'sort-diet-plans-form',
    'saveUrl' => route('admin.diet-plans.sort.update'),
    'redirectUrl' => route('admin.diet-plans.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])