@include('admin.crud.sort', [
    'formId' => 'sort-doctors-form',
    'saveUrl' => route('admin.doctors.sort.update'),
    'redirectUrl' => route('admin.doctors.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'designation']
])