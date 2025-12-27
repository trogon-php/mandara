@include('admin.crud.sort', [
    'page_title' => 'Sort Meal Packages',
    'list_items' => $list_items,
    'sortUrl' => route('admin.meal-packages.sort.update'),
    'redirectUrl' => route('admin.meal-packages.index'),
    'itemLabel' => 'Meal Package',
    'itemNameField' => 'title'
])