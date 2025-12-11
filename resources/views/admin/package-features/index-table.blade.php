@foreach($list_items as $feature)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input bulk-select" value="{{ $feature->id }}">
        </td>
        <td>{{ $feature->id }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $feature->title }}</h6>
                    @if($feature->description)
                        <p class="text-muted mb-0 small">{{ Str::limit($feature->description, 50) }}</p>
                    @endif
                </div>
            </div>
        </td>
        <td>
            @if($feature->package)
                <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ $feature->package->title }}</span>
                    <small class="text-muted">ID: {{ $feature->package->id }}</small>
                </div>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            <span class="badge bg-{{ $feature->status === 'active' ? 'success' : 'secondary' }}">
                {{ ucfirst($feature->status) }}
            </span>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="text-muted small">{{ $feature->created_at->format('M d, Y') }}</span>
                <span class="text-muted small">{{ $feature->created_at->format('h:i A') }}</span>
            </div>
        </td>
        <td>
            @include('admin.crud.action-dropdown', [
                'editUrl' => route('admin.package-features.edit', $feature->id),
                'editTitle' => 'Edit Package Feature',
                'cloneUrl' => route('admin.package-features.clone', $feature->id),
                'cloneTitle' => 'Clone Package Feature',
                'deleteUrl' => route('admin.package-features.destroy', $feature->id),
                'redirectUrl' => url('admin/package-features')
            ])
        </td>
    </tr>
@endforeach

