@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Feed Categories',
    'createUrl'     => url('admin/feed-categories/create'),
    'sortUrl'       => url('admin/feed-categories/sort'),
    'bulkDeleteUrl' => url('admin/feed-categories/bulk-delete'),
    'redirectUrl'   => url('admin/feed-categories'),
    'tableId'       => 'feed-categories-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Feed Categories'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Title</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.feed_categories.index-table', compact('list_items'))
])
