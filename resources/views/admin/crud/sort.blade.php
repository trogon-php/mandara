<form action="{{ $saveUrl }}" method="post" id="{{ $formId ?? 'sort-crud-form' }}" class="ajax-sort-form">
    @csrf

    <div class="alert alert-info mb-3">
        <i class="ri-information-line me-1"></i>
        Drag and drop the items to change their order. Click <strong>"Save Order"</strong> to apply changes.
    </div>

    <ul class="list-group sortable border rounded shadow-soft">
        @foreach ($items as $key => $item)
            <li class="list-group-item d-flex align-items-center bg-white border-bottom sortable-item"
                data-id="{{ $item->id }}">
                <div class="order-number text-muted me-3">{{ $key + 1 }}.</div>
                @php
                $previewImageUrl = null;
                if(isset($previewImageKey) && $item->$previewImageKey) {
                    $previewImageUrl = $item->$previewImageKey;
                } else {
                    $previewImageUrl = $item->image_url ?? $item->thumbnail_url;
                }
                @endphp

                @if($previewImageUrl)
                    <div class="me-3">
                        <img src="{{ $previewImageUrl }}" 
                            alt="{{ data_get($item, $config['title'] ?? 'name') }}" 
                            class="img-thumbnail" 
                            style="width: 75px; height: auto; object-fit: cover;">
                    </div>
                @else
                    <div class="me-3">
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 75px; height: 75px;">
                            <i class="mdi mdi-image text-muted"></i>
                        </div>
                    </div>
                @endif
                
                <div class="flex-grow-1">
                    <b>{{ data_get($item, $config['title'] ?? 'name') }}</b>
                    @if(!empty($config['subtitle']))
                        <small class="text-muted d-block">
                            {{ data_get($item, $config['subtitle']) }}
                        </small>
                    @endif
                    @if(!empty($config['extra']))
                        <p>{{ data_get($item, $config['extra']) }}</p>
                    @endif
                </div>
                <i class="ri-drag-move-2-line text-secondary ms-2 fs-5"></i>
            </li>
        @endforeach
    

    </ul>

    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success btn-save px-4">
            <span class="btn-text"><i class="ri-check-fill me-1"></i> Save Order</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>

<script>
    $(function(){
        initSortableForm({
            form: '#{{ $formId ?? 'sort-crud-form' }}',
            saveUrl: "{{ $saveUrl }}",
            redirectUrl: "{{ $redirectUrl ?? request()->url() }}"
        });
    });
</script>
