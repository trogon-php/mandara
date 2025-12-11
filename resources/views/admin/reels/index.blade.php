@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Reels',
    'createUrl'     => url('admin/reels/create'),
    'sortUrl'       => url('admin/reels/sort'),
    'bulkDeleteUrl' => url('admin/reels/bulk-delete'),
    'redirectUrl'   => url('admin/reels'),
    'tableId'       => 'reels-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Reels'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Reel Categories',
            'url'   => url('admin/reel-categories'),
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-tag'
        ]
    ],
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th>Reel Details</th>
            <th>Category</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.reels.index-table', compact('list_items'))
])
