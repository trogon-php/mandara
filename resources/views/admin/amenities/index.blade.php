@include('admin.crud.crud-index-layout', [
    'page_title' => 'Amenities',
    'createUrl' => url('admin/amenities/create'),
    'sortUrl' => url('admin/amenities/sort'),
    'bulkDeleteUrl' => url('admin/amenities/bulk-delete'),
    'redirectUrl' => url('admin/amenities'),
    'tableId' => 'amenities-table',
    'list_items' => $list_items,
    'breadcrumbs' => ['Dashboard'=>url('admin/dashboard'), 'Amenities'=>null],
    'filters' => view('admin.partials.universal-filters', ['filterConfig'=>$filterConfig,'searchConfig'=>$searchConfig]),
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.amenities.index-table', compact('list_items'))
])