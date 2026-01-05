@include('admin.crud.sort', [
    'formId' => 'sort-nurses-form',
    'saveUrl' => route('admin.nurses.sort.update'),
    'redirectUrl' => route('admin.nurses.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'specialization','extra'=>'qualification','extra'=>'blood_group','extra'=>'date_of_birth','extra'=>'joining_date']
])