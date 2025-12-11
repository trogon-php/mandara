@include('admin.crud.sort', [
    'formId'      => 'sort-testimonials-form',
    'saveUrl'     => route('admin.testimonials.sort.update'),
    'redirectUrl' => route('admin.testimonials.index'),
    'items'       => $list_items,
    'config'      => [
        'title'    => 'user_name',
        'subtitle' => 'designation',
        'extra'    => 'content'
    ]
])