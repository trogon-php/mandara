@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Coupons',
    'createUrl'     => route('admin.coupons.create'),
    'bulkDeleteUrl' => route('admin.coupons.bulk-delete'),
    'redirectUrl'   => route('admin.coupons.index'),
    'tableId'       => 'coupons-table',
    'list_items'    => $coupons,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Coupons'  => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Coupon Details</th>
            <th>Discount</th>
            <th style="width: 250px;">Validity</th>
            <th>Usage</th>
            <th>Status</th>
            <th style="width: 120px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.coupons.index-table', ['coupons' => $coupons])
])
