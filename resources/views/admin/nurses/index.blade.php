@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Nurses',
    'createUrl'     => url('admin/nurses/create'),
    'redirectUrl'   => url('admin/nurses'),
    'tableId'       => 'nurses-table',
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
            <th>Nurse Details</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th>Specialization</th>
            <th>Qualification</th>
            <th>Blood Group</th>
            <th>Date of Birth</th>
            <th>Joining Date</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.nurses.index-table', compact('list_items'))
])