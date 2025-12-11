@include('admin.crud.crud-index-layout', [
    'page_title' => 'Media Library',
    'createUrl' => null,
    'sortUrl' => null,
    'bulkDeleteUrl' => url('admin/media/bulk-delete'),
    'redirectUrl' => url('admin/media'),
    'tableId' => 'media-table',
    'list_items' => $list_items,
    'breadcrumbs' => ['Dashboard' => url('admin/dashboard'), 'Media Library' => null],
    'filters' => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type' => 'button',
            'text' => 'Upload Files',
            'class' => 'btn-primary',
            'icon' => 'mdi mdi-upload',
            'onclick' => "showAjaxModal('" . url('admin/media/create') . "', 'Upload Files')"
        ]
    ],
    'tableHead' => '<tr>
                        <th><input type="checkbox" id="select-all-bulk" class="form-check-input"></th>
                        <th>Preview</th>
                        <th>File Details</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Folder</th>
                        <th>Uploaded</th>
                        <th>Action</th>
                    </tr>',
    'tableBody' => view('admin.media.index-table', compact('list_items'))
])

<script>
    $(document).ready(function() {
        // Copy URL to clipboard
        $('.copy-url-btn').on('click', function() {
            const url = $(this).data('url');
            navigator.clipboard.writeText(url).then(function() {
                messageSuccess('URL copied to clipboard!');
            });
        });

        function viewMedia(id) {
            showAjaxModal('{{ route("admin.media.show", ":id") }}'.replace(':id', id), 'View Media');
            // window.open('{{ url("admin/media") }}/' + id + '/url', '_blank');
        }
        // Make functions global
        window.copyUrl = copyUrl;
        window.viewMedia = viewMedia;
    });

</script>