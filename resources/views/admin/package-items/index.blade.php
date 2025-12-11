@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Package Items',
    'createUrl'     => url('admin/package-items/create'),
    'sortUrl'       => url('admin/package-items/sort'),
    'bulkDeleteUrl' => url('admin/package-items/bulk-delete'),
    'redirectUrl'   => url('admin/package-items'),
    'tableId'       => 'package-items-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Package Items' => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Package</th>
            <th>Item Type</th>
            <th>Item Title</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.package-items.index-table', compact('list_items'))
])