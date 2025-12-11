@foreach($list_items as $package)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $package->id }}">
        </td>
        <td>{{ $package->id }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $package->title }}</h6>
                    @if($package->description)
                        <p class="text-muted mb-0 small">{{ Str::limit($package->description, 50) }}</p>
                    @endif
                    @if($package->hasOffer())
                        <span class="badge bg-success">Special Offer</span>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                @if($package->hasOffer())
                    <span class="text-decoration-line-through text-muted">₹{{ number_format($package->price, 2) }}</span>
                    <span class="text-success fw-bold">₹{{ number_format($package->offer_price, 2) }}</span>
                    <span class="text-success small">{{ $package->discount_percentage }}% off</span>
                @else
                    <span class="fw-bold">₹{{ number_format($package->price, 2) }}</span>
                @endif
            </div>
        </td>
        <td>
            @if($package->duration_days)
                <span class="badge bg-info">{{ $package->duration_days }} days</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            <span class="badge bg-{{ $package->status === 'active' ? 'success' : 'secondary' }}">
                {{ ucfirst($package->status) }}
            </span>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="text-muted small">{{ $package->created_at->format('M d, Y') }}</span>
                <span class="text-muted small">{{ $package->created_at->format('h:i A') }}</span>
            </div>
        </td>
        <td>
            @include('admin.crud.action-dropdown', [
                'editUrl' => route('admin.cottage-packages.edit', $package->id),
                'editTitle' => 'Edit Package',
                'cloneUrl' => route('admin.cottage-packages.clone', $package->id),
                'cloneTitle' => 'Clone Package',
                'deleteUrl' => route('admin.cottage-packages.destroy', $package->id),
                'redirectUrl' => url('admin/cottage-packages')
            ])
        </td>
    </tr>
@endforeach
