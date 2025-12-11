@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Banners',
    'createUrl'     => url('admin/banners/create'),
    'sortUrl'       => url('admin/banners/sort'),
    'bulkDeleteUrl' => url('admin/banners/bulk-delete'),
    'redirectUrl'   => url('admin/banners'),
    'tableId'       => 'banners-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Banners'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th style="width: 150px;">Image</th>
            <th>Title</th>
            <th>Action Type</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.banners.index-table', compact('list_items'))
])

