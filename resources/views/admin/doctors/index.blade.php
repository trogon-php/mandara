@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Doctors',
    'createUrl'     => url('admin/doctors/create'),
    'redirectUrl'   => url('admin/doctors'),
    'tableId'       => 'doctors-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Nurses'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Doctor Details</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th>Designation</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.doctors.index-table', compact('list_items'))
])