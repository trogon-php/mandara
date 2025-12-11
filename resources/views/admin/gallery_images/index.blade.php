@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Gallery Images',
    'createUrl'     => url('admin/gallery-images/create'),
    'sortUrl'       => url('admin/gallery-images/sort'),
    'bulkDeleteUrl' => url('admin/gallery-images/bulk-delete'),
    'redirectUrl'   => url('admin/gallery-images'),
    'tableId'       => 'gallery-images-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Gallery Images'   => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'link',
            'text'  => 'Gallery Albums',
            'url'   => url('admin/gallery-albums'),
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-folder-multiple'
        ]
    ],
    'tableHead'     => '
        <tr>
            <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
            <th>#</th>
            <th style="width: 150px;">Image</th>
            <th>Image Details</th>
            <th>Album</th>
            <th>Status</th>
            <th style="width: 100px;">Updated</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.gallery_images.index-table', compact('list_items'))
])
