@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Estore Delivery Staff',
    'createUrl'     => url('admin/estore-delivery-staff/create'),
    'redirectUrl'   => url('admin/estore-delivery-staff'),
    'tableId'       => 'estore-delivery-staff-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Estore Delivery Staff'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Estore Delivery Staff Details</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.estoredeliverystaff.index-table', compact('list_items'))
])