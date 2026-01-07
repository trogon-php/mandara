@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Food Delivery Staff',
    'createUrl'     => url('admin/food-delivery-staff/create'),
    'redirectUrl'   => url('admin/food-delivery-staff'),
    'tableId'       => 'food-delivery-staff-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Food Delivery Staff'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th>#</th>
            <th>Food Delivery Staff Details</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.fooddeliverystaff.index-table', compact('list_items'))
])