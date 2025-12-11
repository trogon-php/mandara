@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Package Features',
    'createUrl'     => url('admin/package-features/create'),
    'sortUrl'       => url('admin/package-features/sort'),
    'bulkDeleteUrl' => url('admin/package-features/bulk-delete'),
    'redirectUrl'   => url('admin/package-features'),
    'tableId'       => 'package-features-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Package Features'  => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Feature Details</th>
            <th>Package</th>
            <th>Status</th>
            <th style="width: 120px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.package-features.index-table', compact('list_items'))
])
