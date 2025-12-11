@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Reel Categories',
    'createUrl'     => url('admin/reel-categories/create'),
    'sortUrl'       => url('admin/reel-categories/sort'),
    'bulkDeleteUrl' => url('admin/reel-categories/bulk-delete'),
    'redirectUrl'   => url('admin/reel-categories'),
    'tableId'       => 'reel_categories-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Reel Categories'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Reels',
            'url'   => url('admin/reels'),
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-video'
        ]
    ],
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Category Details</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.reel_categories.index-table', compact('list_items'))
])
