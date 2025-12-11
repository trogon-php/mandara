@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Roles',
    'createUrl'     => url('admin/roles/create'),
    'sortUrl'       => url('admin/roles/sort'),
    'bulkDeleteUrl' => url('admin/roles/bulk-delete'),
    'redirectUrl'   => url('admin/roles'),
    'tableId'       => 'roles-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Roles'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>Role ID</th>
            <th>Title</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.roles.index-table', compact('list_items'))
])