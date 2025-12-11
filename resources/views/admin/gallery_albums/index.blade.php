@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Gallery Albums',
    'createUrl'     => url('admin/gallery-albums/create'),
    'sortUrl'       => url('admin/gallery-albums/sort'),
    'bulkDeleteUrl' => url('admin/gallery-albums/bulk-delete'),
    'redirectUrl'   => url('admin/gallery-albums'),
    'tableId'       => 'gallery-albums-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Gallery Albums'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Gallery Images',
            'url'   => url('admin/gallery-images'),
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-image-multiple'
        ]
    ],
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th style="width: 150px;">Thumbnail</th>
            <th>Album Details</th>
            <th>Images Count</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.gallery_albums.index-table', compact('list_items'))
])
