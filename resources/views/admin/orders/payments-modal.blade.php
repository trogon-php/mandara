@php
    $order = $order ?? null;
    $payments = $payments ?? collect();
@endphp

@if($order)
    <div class="row mb-3">
        <div class="col-md-6">
            <h6 class="mb-1">Order: {{ $order->order_number }}</h6>
            <p class="text-muted mb-0">Customer: {{ $order->user->name ?? 'N/A' }}</p>
            <p class="text-muted mb-0">Package: {{ $order->package->title ?? 'N/A' }}</p>
        </div>
        <div class="col-md-6 text-end">
            <h6 class="mb-1">Total Amount: ₹{{ number_format($order->amount_total, 2) }}</h6>
            <p class="text-muted mb-0">Final Amount: ₹{{ number_format($order->amount_final, 2) }}</p>
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
            <span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
        </div>
    </div>
    <hr>

    @if($payments->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        @php
                            $paymentStatusColors = [
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'secondary'
                            ];
                            $paymentStatusColor = $paymentStatusColors[$payment->payment_status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td><code>{{ $payment->transaction_id ?: 'N/A' }}</code></td>
                            <td class="fw-bold">₹{{ number_format($payment->amount_paid, 2) }}</td>
                            <td><span class="badge bg-{{ $paymentStatusColor }}">{{ strtoupper($payment->payment_status) }}</span></td>
                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $payment->remarks ?: 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4">
            <i class="ri-money-dollar-circle-line text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">No Payments Found</h5>
            <p class="text-muted">This order doesn't have any payments yet.</p>
        </div>
    @endif
@else
    <div class="alert alert-danger">
        <i class="ri-error-warning-line me-2"></i>
        Order not found
    </div>
@endif


