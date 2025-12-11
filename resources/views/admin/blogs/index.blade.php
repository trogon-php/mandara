@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Blogs',
    'createUrl'     => url('admin/blogs/create'),
    'sortUrl'       => url('admin/blogs/sort'),
    'bulkDeleteUrl' => url('admin/blogs/bulk-delete'),
    'redirectUrl'   => url('admin/blogs'),
    'tableId'       => 'blogs-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Blogs'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th style="width: 150px;">Image</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Short Description</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.blogs.index-table', compact('list_items'))
])