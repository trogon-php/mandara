@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Testimonials',
    'createUrl'     => url('admin/testimonials/create'),
    'sortUrl'       => url('admin/testimonials/sort'),
    'bulkDeleteUrl' => url('admin/testimonials/bulk-delete'),
    'redirectUrl'   => url('admin/testimonials'),
    'tableId'       => 'testimonials-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Testimonials'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Content</th>
            <th>Status</th>
            <th style="width: 120px;">User Details</th>
            <th style="width: 100px;">Rating</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.testimonials.index-table', compact('list_items'))
])