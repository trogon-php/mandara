@include('admin.crud.form', [
    'action' => route('admin.media.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-media-form',
    'submitText' => 'Update Media',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.media.index'),
    'fields' => [
        ['type'=>'text','name'=>'name','label'=>'Name','value'=>old('name',$edit_data->name),'col'=>6],
        ['type'=>'select','name'=>'status','label'=>'Status','options'=>[1=>'Published',0=>'Draft'],'value'=>old('status',$edit_data->status),'col'=>6]
    ]
])