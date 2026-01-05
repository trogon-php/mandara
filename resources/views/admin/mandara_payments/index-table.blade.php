@foreach($list_items as $payment)
    <tr>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $payment->payment_order_id ?? 'N/A' }}</h6>
                
                <small class="text-muted">
                    <i class="ri-calendar-line"></i> {{ $payment->created_at->format('M d, Y h:i A') }}
                </small>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $payment->booking->user->name ?? 'N/A' }}</h6>
                    <p class="text-muted mb-0 small">{{ $payment->booking->user->email ?? 'N/A' }}</p>
                    @if($payment->booking)
                        <p class="text-muted mb-0 small">{{ $payment->booking->user->phone ?? 'N/A' }}</p>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $payment->booking->booking_number ?? 'N/A' }}</h6>
                @if($payment->total_amount)
                    <small class="text-muted">
                        <i class="ri-money-dollar-circle-line"></i> ₹{{ number_format($payment->total_amount, 2) }}
                    </small>
                @endif
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-1">{{ $payment->booking->cottagePackage->title ?? 'N/A' }}</h6>
                @if($payment->booking && $payment->booking->cottagePackage)
                    <div class="text-muted small">
                        <span class="fw-bold text-primary">₹{{ number_format($payment->booking?->cottagePackage->price , 2) }}</span>
                        @if($payment->booking->cottagePackage->offer_price && $payment->booking?->cottagePackage->offer_price != $payment->booking->cottagePackage->price)
                            <span class="text-success ms-2">₹{{ number_format($payment->booking?->cottagePackage->offer_price, 2) }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="fw-bold text-primary">₹{{ number_format($payment->payable_amount, 2) }}</span>
                
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
                $statusColor = $statusColors[$payment->booking?->booking_payment_status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $statusColor }}">
                {{ ucfirst($payment->booking?->booking_payment_status) }}
            </span>
        </td>
        <td>
            {{-- @include('admin.crud.action-dropdown', [
                'viewUrl' => route('admin.payments.show', $payment->id),
                'viewTitle' => 'View Payment',
                'deleteUrl' => route('admin.payments.destroy', $payment->id),
                'redirectUrl' => url('admin/payments'),
                
            ]) --}}
        </td>
    </tr>
@endforeach
