@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Attendants',
    'createUrl'     => url('admin/attendants/create'),
    'redirectUrl'   => url('admin/attendants'),
    'tableId'       => 'attendants-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Attendants'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Attendant Details</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.attendants.index-table', compact('list_items'))
])