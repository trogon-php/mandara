@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Stay Bookings',
    'createPage'     => url('admin/mandara-bookings/create'),
    'bulkDeleteUrl' => url('admin/mandara-bookings/bulk-delete'),
    'redirectUrl'   => url('admin/mandara-bookings'),
    'tableId'       => 'mandara-bookings-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Stay Bookings'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Booking Number</th>
            <th>Cottage Package</th>
            <th>User Name</th>
            <th>Delivery Status</th>
            <th>Arrival Date</th>
            <th>Departure Date</th>
            <th>Approval Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.mandara_bookings.index-table', compact('list_items'))
])