@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Notifications',
    'createUrl'     => url('admin/notifications/create'),
    'sortUrl'       => url('admin/notifications/sort'),
    'bulkDeleteUrl' => url('admin/notifications/bulk-delete'),
    'redirectUrl'   => url('admin/notifications'),
    'tableId'       => 'notifications-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Notifications'   => null,
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
            <th>Course</th>
            <th>Category</th>
            <th>Premium</th>
            <th>Image</th>
            <th>Views</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.notifications.index-table', compact('list_items'))
])