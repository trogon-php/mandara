@foreach($list_items as $order)
    <tr>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $order->order_number }}</h6>
                @if($order->coupon_code)
                    <small class="text-muted">
                        <i class="ri-coupon-line"></i> {{ $order->coupon_code }}
                    </small>
                @endif
                <small class="text-muted">
                    <i class="ri-calendar-line"></i> {{ $order->created_at->format('M d, Y h:i A') }}
                </small>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $order->user->name ?? 'N/A' }}</h6>
                    <p class="text-muted mb-0 small">{{ $order->user->email ?? 'N/A' }}</p>
                    @if($order->user->phone)
                        <p class="text-muted mb-0 small">{{ $order->user->phone }}</p>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $order->package->title ?? 'N/A' }}</h6>
                @if($order->package)
                    <div class="text-muted small">
                        <span class="fw-bold text-primary">₹{{ number_format($order->package->price, 2) }}</span>
                        @if($order->package->offer_price && $order->package->offer_price != $order->package->price)
                            <span class="text-success ms-2">₹{{ number_format($order->package->offer_price, 2) }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="fw-bold text-primary">₹{{ number_format($order->amount_final, 2) }}</span>
                @if($order->amount_offer > 0)
                    <small class="text-success">
                        <i class="ri-discount-line"></i> ₹{{ number_format($order->amount_offer, 2) }} off
                    </small>
                @endif
                @if($order->amount_total != $order->amount_final)
                    <small class="text-muted text-decoration-line-through">
                        ₹{{ number_format($order->amount_total, 2) }}
                    </small>
                @endif
            </div>
        </td>
        <td>
            @php
                $statusColors = [
                    'pending' => 'warning',
                    'partially_paid' => 'info',
                    'paid' => 'success',
                    'cancelled' => 'danger',
                    'refunded' => 'secondary'
                ];
                $statusColor = $statusColors[$order->status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $statusColor }}">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </td>
        <td>
            @include('admin.crud.action-dropdown', [
                'viewUrl' => route('admin.orders.show', $order->id),
                'viewTitle' => 'View Order',
                'editUrl' => route('admin.orders.edit', $order->id),
                'editTitle' => 'Edit Order',
                'deleteUrl' => route('admin.orders.destroy', $order->id),
                'redirectUrl' => url('admin/orders'),
                'customActions' => [
                    [
                        'url' => 'javascript:void(0)',
                        'title' => 'Update Status',
                        'icon' => 'ri-refresh-line',
                        'class' => 'text-warning',
                        'onclick' => 'showStatusModal(' . $order->id . ', "' . $order->status . '")'
                    ],
                    [
                        'url' => 'javascript:void(0)',
                        'title' => 'View Payments',
                        'icon' => 'ri-money-dollar-circle-line',
                        'class' => 'text-info',
                        'onclick' => 'viewOrderPayments(' . $order->id . ')'
                    ]
                ]
            ])
        </td>
    </tr>
@endforeach


