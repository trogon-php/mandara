@include('admin.crud.crud-index-layout', [
    'page_title' => 'Assignments',
    'createUrl' => url('admin/assignments/create'),
    // 'sortUrl' => url('admin/assignments/sort'),
    'bulkDeleteUrl' => url('admin/assignments/bulk-delete'),
    'redirectUrl' => url('admin/assignments'),
    'tableId' => 'assignments-table',
    'list_items' => $list_items,
    'breadcrumbs' => [
        'Dashboard' => url('admin/dashboard'),
        'Assignments'=>null
    ],
    'filters' => view('admin.partials.universal-filters',['filterConfig'=>$filterConfig,'searchConfig'=>$searchConfig]),
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>Assignment Details</th>
                        <th>Max Marks</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.assignments.index-table', compact('list_items'))
])