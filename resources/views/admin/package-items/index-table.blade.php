@foreach($list_items as $index => $item)
<tr>
    <td>
        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}">
    </td>
    <td>{{ $index + 1 }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div class="me-2">
                <h5>{{ $item->package->title ?? 'N/A' }}</h5>
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-info">Course</span>
    </td>
    <td>
        <div class="d-flex flex-column">
            <span class="fw-bold">{{ $item->item_title ?? 'N/A' }}</span>
            @if($item->course)
                <small class="text-muted">
                    {{ $item->course->title ?? 'N/A' }}
                </small>
            @endif
        </div>
    </td>
    <td>
        @if($item->status === 'active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
    </td>
    <td>
        <small class="text-muted">
            {{ $item->updated_at ? $item->updated_at->format('M d, Y') : 'N/A' }}
        </small>
    </td>
    <td class="text-center">
        @include('admin.crud.action-dropdown', [
            'viewUrl'     => url('admin/package-items/' . $item->id),
            'viewTitle'   => 'View Package Item',
            'editUrl'     => url('admin/package-items/' . $item->id . '/edit'),
            'editTitle'   => 'Update Package Item',
            'deleteUrl'   => route('admin.package-items.destroy', $item->id),
            'redirectUrl' => route('admin.package-items.index')
        ])
    </td>
</tr>
@endforeach