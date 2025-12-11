@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Feeds',
    'createUrl'     => url('admin/feeds/create'),
    'sortUrl'       => url('admin/feeds/sort'),
    'bulkDeleteUrl' => url('admin/feeds/bulk-delete'),
    'redirectUrl'   => url('admin/feeds'),
    'tableId'       => 'feeds-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Feeds'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Content</th>
            <th>Files</th>
            <th>Category</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.feeds.index-table', compact('list_items'))
])
