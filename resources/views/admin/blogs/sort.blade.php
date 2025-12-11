@include('admin.crud.sort', [
    'formId' => 'sort-blogs-form',
    'saveUrl' => route('admin.blogs.sort.update'),
    'redirectUrl' => route('admin.blogs.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])