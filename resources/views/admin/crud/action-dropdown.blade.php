<div class="dropdown d-inline-block">
    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ri-more-fill align-middle"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        {{-- View Details --}}
        @if(isset($viewUrl) && isset($viewTitle) && isset($viewType) && $viewType === 'link')
        <li>
            <a href="{{ $viewUrl }}" class="dropdown-item">
                <i class="ri-eye-fill align-bottom me-2 text-info"></i> View Details
            </a>
        </li>
        @elseif(isset($viewUrl) && isset($viewTitle))
        <li>
                <a href="javascript:void(0)" class="dropdown-item" 
                   onclick="showAjaxModal('{{ $viewUrl }}', '{{ $viewTitle }}')">
                    <i class="ri-eye-fill align-bottom me-2 text-info"></i> View Details
                </a>
            </li>
        @endif

        {{-- Edit --}}
        @if(isset($editUrl) && isset($editTitle))
        <li>
            <a href="javascript:void(0)" class="dropdown-item edit-item-btn" 
               onclick="showAjaxModal('{{ $editUrl }}', '{{ $editTitle }}')">
                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
            </a>
        </li>
        @endif

        {{-- Clone --}}
        @if(isset($cloneUrl) && isset($cloneTitle))
        <li>
            <a href="javascript:void(0)" class="dropdown-item edit-item-btn" 
               onclick="confirmClone('{{ $cloneUrl }}', '{{ $cloneTitle }}')">
                <i class="ri-file-copy-line align-bottom me-2 text-muted"></i> Clone
            </a>
        </li>
        @endif

        {{-- Custom Actions --}}
        @if(isset($customActions) && is_array($customActions))
            @foreach($customActions as $action)
            <li>
                <a href="{{ $action['url'] }}" 
                   class="dropdown-item {{ $action['class'] ?? '' }}"
                   @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif>
                    <i class="{{ $action['icon'] ?? 'ri-more-line' }} align-bottom me-2"></i> {{ $action['title'] }}
                </a>
            </li>
            @endforeach
        @endif

        {{-- Delete --}}
        @if(isset($deleteUrl))
        <li>
            <a href="javascript:void(0)" class="dropdown-item remove-item-btn" 
               onclick="confirmDelete('{{ $deleteUrl }}', '{{ $redirectUrl ?? '' }}')">
                <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Delete
            </a>
        </li>
        @endif
    </ul>
</div>