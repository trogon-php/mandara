@foreach($list_items as $package)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $package->id }}">
        </td>
        <td>{{ $loop->iteration }}</td>
        <td>
            <div class="d-flex align-items-start">
                <div class="avatar-sm bg-opacity-10 rounded d-flex align-items-center justify-content-center me-3" style="min-width: 45px; height: 45px;">
                    <i class="mdi mdi-package-variant text-primary" style="font-size: 20px;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-semibold">{{ $package->title }}</h6>
                    @if($package->description)
                        <p class="text-muted mb-0 small" style="max-width: 280px;">{{ Str::limit($package->description, 60) }}</p>
                    @endif
                    @if($package->hasDiscount())
                        <span class="badge bg-success bg-opacity-15 text-white mt-1">
                            <i class="mdi mdi-tag-outline me-1"></i>Special Discount
                        </span>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column align-items-start">
                @if($package->hasDiscount())
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-decoration-line-through text-muted small">₹{{ number_format($package->price, 2) }}</span>
                        <span class="badge bg-success bg-opacity-15 text-white" style="font-size: 10px;">
                            {{ round(($package->discount_amount / $package->price) * 100) }}% OFF
                        </span>
                    </div>
                    <span class="fw-bold text-success" style="font-size: 15px;">
                        <i class="mdi mdi-currency-inr"></i>{{ number_format($package->price - $package->discount_amount, 2) }}
                    </span>
                @else
                    <span class="fw-bold text-dark" style="font-size: 15px;">
                        <i class="mdi mdi-currency-inr text-muted"></i>{{ number_format($package->price, 2) }}
                    </span>
                @endif
            </div>
        </td>
        <td>
            @if($package->duration_days)
                <div class="d-flex align-items-center">
                    <span class="badge bg-info bg-opacity-15 text-white px-2 py-1">
                        <i class="mdi mdi-clock-outline me-1"></i>{{ $package->duration_days }} {{ Str::plural('Day', $package->duration_days) }}
                    </span>
                </div>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if($package->status === 'active')
                <span class="badge bg-success bg-opacity-15 text-white px-2 py-1">
                    <i class="mdi mdi-check-circle me-1"></i>Active
                </span>
            @else
                <span class="badge bg-secondary bg-opacity-15 text-white px-2 py-1">
                    <i class="mdi mdi-close-circle me-1"></i>Inactive
                </span>
            @endif
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="small fw-medium">{{ $package->created_at->format('d M, Y') }}</span>
                <span class="text-muted small">
                    <i class="mdi mdi-clock-outline" style="font-size: 10px;"></i> 
                    {{ $package->created_at->format('h:i A') }}
                </span>
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
