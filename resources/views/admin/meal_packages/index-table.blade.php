@if($list_items)
    @foreach($list_items as $list_item)
        <tr class="meal-package-row">
            <td>
                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $list_item->id }}">
            </td>
            <td>
                <span class="text-muted fw-medium">{{ $loop->iteration }}</span>
            </td>
            <td>
                <div class="meal-package-thumbnail">
                    @if($list_item->thumbnail)
                        <img src="{{ $list_item->thumbnail_url }}" 
                             alt="{{ $list_item->title }}" 
                             class="rounded"
                             style="width: 70px; height: 70px; object-fit: cover; border: 2px solid #e9ecef;">
                    @else
                        <div class="d-flex align-items-center justify-content-center rounded bg-light" 
                             style="width: 70px; height: 70px; border: 2px solid #e9ecef;">
                            <i class="mdi mdi-image-off text-muted" style="font-size: 24px;"></i>
                        </div>
                    @endif
                </div>
            </td>
            <td>
                <div class="d-flex flex-column">
                    <h6 class="mb-1 fw-semibold text-dark">{{ $list_item->title }}</h6>
                    @if($list_item->short_description)
                        <p class="text-muted mb-0 small" style="max-width: 300px; line-height: 1.4;">
                            {{ Str::limit(strip_tags($list_item->short_description), 80) }}
                        </p>
                    @endif
                </div>
            </td>
            <td>
                @if($list_item->labels)
                    @php
                        $labels = array_filter(array_map('trim', explode(',', $list_item->labels)));
                    @endphp
                    @if(count($labels) > 0)
                        <div class="d-flex flex-wrap gap-1" style="max-width: 200px;">
                            @foreach($labels as $label)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">
                                    <i class="mdi mdi-tag-outline me-1" style="font-size: 12px;"></i>
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                @else
                    <span class="text-muted small">—</span>
                @endif
            </td>
            <td>
                @if($list_item->is_veg)
                    <span class="badge bg-success bg-opacity-15 text-white px-3 py-2 d-inline-flex align-items-center">
                        <i class="mdi mdi-leaf me-1"></i>
                        <span>Veg</span>
                    </span>
                @else
                    <span class="badge bg-danger bg-opacity-15 text-white px-3 py-2 d-inline-flex align-items-center">
                        <i class="mdi mdi-food-drumstick me-1"></i>
                        <span>Non-Veg</span>
                    </span>
                @endif
            </td>
            <td>
                @if($list_item->status)
                    <span class="badge bg-success bg-opacity-15 text-white px-3 py-2 d-inline-flex align-items-center">
                        <i class="mdi mdi-check-circle me-1"></i>
                        <span>Active</span>
                    </span>
                @else
                    <span class="badge bg-secondary bg-opacity-15 text-secondary px-3 py-2 d-inline-flex align-items-center">
                        <i class="mdi mdi-close-circle me-1"></i>
                        <span>Inactive</span>
                    </span>
                @endif
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'cloneUrl' => url('admin/meal-packages/' . $list_item->id . '/clone'),
                    'cloneTitle' => 'Clone Meal Package',
                    'editTitle' => 'Edit Meal Package',
                    'editUrl' => url('admin/meal-packages/' . $list_item->id . '/edit'),
                    'deleteUrl' => route('admin.meal-packages.destroy', $list_item->id),
                    'redirectUrl' => route('admin.meal-packages.index')
                ])
            </td>
        </tr>
    @endforeach
@endif

<style>
.meal-package-row {
    transition: all 0.3s ease;
}

.meal-package-row:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.meal-package-row td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.meal-package-thumbnail {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.meal-package-row:hover .meal-package-thumbnail img,
.meal-package-row:hover .meal-package-thumbnail div {
    transform: scale(1.05);
}

.meal-package-thumbnail img {
    transition: transform 0.3s ease;
}

.meal-package-thumbnail div {
    transition: transform 0.3s ease;
}

.badge {
    font-weight: 500;
    font-size: 0.75rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .meal-package-row td {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .meal-package-thumbnail img,
    .meal-package-thumbnail div {
        width: 50px !important;
        height: 50px !important;
    }
}
</style>