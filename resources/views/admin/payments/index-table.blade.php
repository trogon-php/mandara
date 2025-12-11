@foreach($list_items as $payment)
    <tr>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $payment->transaction_id ?? 'N/A' }}</h6>
                @if($payment->remarks)
                    <small class="text-muted">
                        <i class="ri-message-line"></i> {{ Str::limit($payment->remarks, 30) }}
                    </small>
                @endif
                <small class="text-muted">
                    <i class="ri-calendar-line"></i> {{ $payment->created_at->format('M d, Y h:i A') }}
                </small>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $payment->user->name ?? 'N/A' }}</h6>
                    <p class="text-muted mb-0 small">{{ $payment->user->email ?? 'N/A' }}</p>
                    @if($payment->user->phone)
                        <p class="text-muted mb-0 small">{{ $payment->user->phone }}</p>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $payment->order->order_number ?? 'N/A' }}</h6>
                @if($payment->order)
                    <small class="text-muted">
                        <i class="ri-money-dollar-circle-line"></i> ₹{{ number_format($payment->order->amount_final, 2) }}
                    </small>
                @endif
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $payment->package->title ?? 'N/A' }}</h6>
                @if($payment->package)
                    <div class="text-muted small">
                        <span class="fw-bold text-primary">₹{{ number_format($payment->package->price, 2) }}</span>
                        @if($payment->package->offer_price && $payment->package->offer_price != $payment->package->price)
                            <span class="text-success ms-2">₹{{ number_format($payment->package->offer_price, 2) }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="fw-bold text-primary">₹{{ number_format($payment->amount_paid, 2) }}</span>
                @if($payment->amount_total != $payment->amount_paid)
                    <small class="text-muted">
                        <i class="ri-money-dollar-circle-line"></i> Total: ₹{{ number_format($payment->amount_total, 2) }}
                    </small>
                @endif
            </div>
        </td>
        <td>
            @php
                $statusColors = [
                    'pending' => 'warning',
                    'paid' => 'success',
                    'failed' => 'danger',
                    'refunded' => 'secondary'
                ];
                $statusColor = $statusColors[$payment->payment_status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $statusColor }}">
                {{ ucfirst($payment->payment_status) }}
            </span>
        </td>
        <td>
            @include('admin.crud.action-dropdown', [
                'viewUrl' => route('admin.payments.show', $payment->id),
                'viewTitle' => 'View Payment',
                'editUrl' => route('admin.payments.edit', $payment->id),
                'editTitle' => 'Edit Payment',
                'deleteUrl' => route('admin.payments.destroy', $payment->id),
                'redirectUrl' => url('admin/payments'),
                'customActions' => [
                    [
                        'url' => 'javascript:void(0)',
                        'title' => 'Update Status',
                        'icon' => 'ri-refresh-line',
                        'class' => 'text-warning',
                        'onclick' => 'showPaymentStatusModal(' . $payment->id . ', "' . $payment->payment_status . '")'
                    ]
                ]
            ])
        </td>
    </tr>
@endforeach
