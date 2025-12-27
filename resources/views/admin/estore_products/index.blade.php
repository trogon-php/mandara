@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Estore Products',
    'createUrl'     => url('admin/estore-products/create'),
    'sortUrl'       => url('admin/estore-products/sort'),
    'bulkDeleteUrl' => url('admin/estore-products/bulk-delete'),
    'redirectUrl'   => url('admin/estore-products'),
    'tableId'       => 'estore-products-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Estore Products'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th style="width: 150px;">Images</th>
            <th>Title</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Category</th>
            <th>Price</th>
            <th>MRP</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.estore_products.index-table', compact('list_items'))
])