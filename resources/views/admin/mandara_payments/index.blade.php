@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Mandara Booking Payments',
    'redirectUrl'   => url('admin/mandara-payments'),
    'tableId'       => 'mandara-payments-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Mandara Booking Payments'    => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    
    'tableHead'     => '
        <tr>
            <th>Payment Details</th>
            <th>User</th>
            <th>Booking</th>
            <th>Package</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody' => view('admin.mandara_payments.index-table', compact('list_items'))
])




