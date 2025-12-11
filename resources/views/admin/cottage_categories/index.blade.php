@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Cottage Categories',
    'createUrl'     => url('admin/cottage-categories/create'),
    'sortUrl'       => url('admin/cottage-categories/sort'),
    'bulkDeleteUrl' => url('admin/cottage-categories/bulk-delete'),
    'redirectUrl'   => url('admin/cottage-categories'),
    'tableId'       => 'cottage-categories-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Cottage Categories'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th style="width: 150px;">Thumbnail</th>
            <th>Title</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.cottage_categories.index-table', compact('list_items'))
])