@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Packages',
    'createUrl'     => url('admin/packages/create'),
    'sortUrl'       => url('admin/packages/sort'),
    'bulkDeleteUrl' => url('admin/packages/bulk-delete'),
    'redirectUrl'   => url('admin/packages'),
    'tableId'       => 'packages-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Packages'  => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Package Items',
            'url'   => url('admin/package-items'),
            'class' => 'btn-outline-success',
            'icon'  => 'mdi mdi-tag-multiple',
        ],
        [
            'type'  => 'link',
            'text'  => 'Package Features',
            'url'   => url('admin/package-features'),
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-tag-multiple',
        ]
    ],
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Package Details</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Items</th>
            <th>Status</th>
            <th style="width: 120px;">Created</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.packages.index-table', ['list_items' => $list_items])
])
