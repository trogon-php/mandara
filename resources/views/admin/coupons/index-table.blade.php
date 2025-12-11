@foreach($coupons as $coupon)
<tr>
    <td>
        <input type="checkbox" name="bulk_ids[]" value="{{ $coupon->id }}" class="form-check-input bulk-checkbox">
    </td>
    <td>{{ $coupon->id }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <h6 class="mb-1">{{ $coupon->title }}</h6>
                <div class="mb-1">
                    <span class="badge bg-info fs-6 px-2 py-1">
                        <i class="mdi mdi-tag me-1"></i>{{ $coupon->code }}
                    </span>
                </div>
                @if($coupon->description)
                    <small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                @endif
            </div>
        </div>
    </td>
    <td>
        <div class="text-center">
            @if($coupon->discount_type === 'percentage')
                <span class="text-success fw-bold fs-6">
                    {{ $coupon->discount_value }}%
                </span>
            @else
                <span class="badge bg-success fs-6 px-3 py-2">
                    ₹{{ $coupon->discount_value }}
                </span>
            @endif
        </div>
    </td>
    <td>
        <div class="d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted">
                    <strong>From:</strong> {{ $coupon->start_date->format('M d, Y') }}
                </small>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">
                    <strong>To:</strong> {{ $coupon->end_date->format('M d, Y') }}
                </small>
            </div>
            <div class="text-center">
                @if($coupon->end_date->isPast())
                    <span class="badge border border-danger text-danger bg-light" style="border-width: 1px !important;">
                        <i class="mdi mdi-clock-alert me-1"></i>Expired
                    </span>
                @elseif($coupon->start_date->isFuture())
                    <span class="badge border border-warning text-warning bg-light" style="border-width: 1px !important;">
                        <i class="mdi mdi-clock-outline me-1"></i>Not Started
                    </span>
                @else
                    <span class="badge border border-success text-success bg-light" style="border-width: 1px !important;">
                        <i class="mdi mdi-check-circle me-1"></i>Active
                    </span>
                @endif
            </div>
        </div>
    </td>
    <td>
        <div class="text-center">
            <div class="mb-1">
                <strong class="text-primary">{{ $coupon->usages->count() ?? 0 }}</strong>
                <small class="text-muted"> used</small>
            </div>
            @if($coupon->usage_limit)
                <div>
                    <small class="text-muted">of </small>
                    <strong class="text-secondary">{{ $coupon->usage_limit }}</strong>
                </div>
                @if($coupon->usages->count() >= $coupon->usage_limit)
                    <span class="badge bg-danger mt-1" style="font-size: 10px;">Limit Reached</span>
                @endif
            @else
                <small class="text-success">Unlimited</small>
            @endif
        </div>
    </td>
    <td>
        <span class="badge bg-{{ $coupon->status === 'active' ? 'success' : 'secondary' }}">
            {{ ucfirst($coupon->status) }}
        </span>
    </td>
    <td>
        <small class="text-muted">{{ $coupon->created_at->format('M d, Y') }}</small>
    </td>
    <td>
        @include('admin.crud.action-dropdown', [
            'editUrl' => route('admin.coupons.edit', $coupon->id),
            'deleteUrl' => route('admin.coupons.destroy', $coupon->id),
            'redirectUrl' => route('admin.coupons.index')
        ])
    </td>
</tr>
@endforeach
