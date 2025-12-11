@include('admin.crud.sort', [
    'formId' => 'sort-media-form',
    'saveUrl' => route('admin.media.sort.update'),
    'redirectUrl' => route('admin.media.index'),
    'items' => $list_items,
    'config' => ['title'=>'name','subtitle'=>'subtitle','extra'=>'status']
])