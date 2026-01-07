@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Front Office',
    'createUrl'     => url('admin/front-office/create'),
    'redirectUrl'   => url('admin/front-office'),
    'tableId'       => 'front-office-table',
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
            <th>Front Office Details</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th style="width: 100px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.frontoffice.index-table', compact('list_items'))
])