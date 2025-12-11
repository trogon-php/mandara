@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Cottage Packages',
    'createUrl'     => url('admin/cottage-packages/create'),
    'sortUrl'       => url('admin/cottage-packages/sort'),
    'bulkDeleteUrl' => url('admin/cottage-packages/bulk-delete'),
    'redirectUrl'   => url('admin/cottage-packages'),
    'tableId'       => 'cottage-packages-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Cottage Packages'  => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Package Details</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Status</th>
            <th style="width: 120px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.cottage_packages.index-table', ['list_items' => $list_items])
])
