@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Client Credentials',
    'createUrl'     => url('admin/client-credentials/create'),
    'bulkDeleteUrl' => url('admin/client-credentials/bulk-delete'),
    'redirectUrl'   => url('admin/client-credentials'),
    'tableId'       => 'client-credentials-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'System' => null,
        'Client Credentials' => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [],
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>Provider</th>
            <th>Credential Details</th>
            <th>Account Credentials</th>
            <th>Remarks</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.client_credentials.index-table', compact('list_items'))
])
