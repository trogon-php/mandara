@isset($bulkDeleteUrl)
    <button type="button" id="bulk-delete-btn"
            class="btn btn-danger btn-sm rounded-pill float-start me-2 mb-2 d-none"
            onclick="deleteSelected('{{ $bulkDeleteUrl }}', '{{ $redirectUrl ?? '' }}')">
        <i class="ri-delete-bin-line"></i> Delete Selected
    </button>
@endisset
