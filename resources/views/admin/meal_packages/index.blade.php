@include('admin.crud.crud-index-layout', [
    'page_title' => 'Meal Packages',
    'createUrl' => url('admin/meal-packages/create'),
    'sortUrl' => url('admin/meal-packages/sort'),
    'bulkDeleteUrl' => url('admin/meal-packages/bulk-delete'),
    'redirectUrl' => url('admin/meal-packages'),
    'tableId' => 'meal-packages-table',
    'list_items' => $list_items,
    'breadcrumbs' => ['Dashboard' => url('admin/dashboard'), 'Meal Packages' => null],
    'filters' => view('admin.partials.universal-filters', ['filterConfig' => $filterConfig, 'searchConfig' => $searchConfig]),
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>#</th>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Labels</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.meal_packages.index-table', compact('list_items'))
])