@include('admin.crud.form', [
    'action' => route('admin.media.store'),
    'formId' => 'add-media-form',
    'submitText' => 'Save Media',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.media.index'),
    'fields' => [
        [
            'type'=>'files',
            'name'=>'media_files',
            'label'=>'Files',
            'multiple' => true,
            'col'=>12,
            'pasteable' => true,
            'presetKey' => 'media_files',
            'accept' => 'image/*,video/*,audio/*,application/pdf',
            'required' => true
        ],
        [
            'type'=>'text',
            'name'=>'folder',
            'label'=>'Folder (optional)',
            'placeholder'=>'Enter folder name here',
            'col'=>12,
        ]
    ]
])