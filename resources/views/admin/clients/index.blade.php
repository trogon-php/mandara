@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Clients',
    'createUrl'     => url('admin/clients/create'),
    'redirectUrl'   => url('admin/clients'),
    'tableId'       => 'clients-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Clients'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Client Details</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.clients.index-table', compact('list_items'))
])