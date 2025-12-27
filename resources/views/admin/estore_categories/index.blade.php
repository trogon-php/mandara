@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Estore Categories',
    'createUrl'     => url('admin/estore-categories/create'),
    'sortUrl'       => url('admin/estore-categories/sort'),
    'bulkDeleteUrl' => url('admin/estore-categories/bulk-delete'),
    'redirectUrl'   => url('admin/estore-categories'),
    'tableId'       => 'estore-categories-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Estore Categories'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Title</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.estore_categories.index-table', compact('list_items'))
])