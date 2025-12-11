@include('admin.crud.form', [
    'action' => route('admin.assignments.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-assignments-form',
    'submitText' => 'Update Assignments',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.assignments.index'),
    'fields' => [
        [
            'type'  =>'text',
            'name'=>'title',
            'label'=>'Title',
            'value'=>old('title',$edit_data->title),'col'=>6
        ],
        [
            'type'=>'select',
            'name'=>'course_id',
            'label'=>'Course',
            'options'=>$courses,
            'value'=>old('course_id',$edit_data->course_id),'col'=>6
        ],
        [
            'type'=>'textarea',
            'name'=>'description',
            'label'=>'Description',
            'value'=>old('description',$edit_data->description),'col'=>12
        ],
        [
            'type'=>'datetime',
            'name'=>'due_date',
            'label'=>'Due Date',
            'value'=>old('due_date',$edit_data->due_date),'col'=>6
        ],
        [
            'type'=>'number',
            'name'=>'max_marks',
            'label'=>'Max Marks',
            'value'=>old('max_marks',$edit_data->max_marks),'col'=>6
        ],
        [
            'type' => 'files',
            'name' => 'assignment_files',
            'id' => 'assignment_files',
            'label' => 'Assignment Files',
            'presetKey' => 'assignments_files',
            'multiple' => true,
            'accept' => 'file/*',
            'value' => $edit_data->files_url,
            'col' => 12
        ],
        [
            'type'=>'select',
            'name'=>'status',
            'label'=>'Status',
            'options'=>[
                'active'   =>  'Active',
                'inactive'   =>  'Inactive'
            ],
            'value'=>old('status',$edit_data->status),'col'=>6
        ]
    ]
])