@include('admin.crud.crud-index-layout', [
    'page_title' => 'Cottages',
    'createUrl' => url('admin/cottages/create'),
    'sortUrl' => url('admin/cottages/sort'),
    'bulkDeleteUrl' => url('admin/cottages/bulk-delete'),
    'redirectUrl' => url('admin/cottages'),
    'tableId' => 'cottages-table',
    'list_items' => $list_items,
    'breadcrumbs' => ['Dashboard'=>url('admin/dashboard'), 'Cottages'=>null],
    'filters' => view('admin.partials.universal-filters', ['filterConfig'=>$filterConfig,'searchConfig'=>$searchConfig]),
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.cottages.index-table', compact('list_items'))
])