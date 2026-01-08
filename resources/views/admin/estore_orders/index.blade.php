@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Estore Orders',
    'redirectUrl'   => url('admin/estore-orders'),
    'tableId'       => 'estore-orders-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Estore Orders' => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Amount</th>
            <th>Order Status</th>
            <th>Payment Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody' => view('admin.estore_orders.index-table', compact('list_items'))
])
@include('admin.estore_orders.assign-modal')