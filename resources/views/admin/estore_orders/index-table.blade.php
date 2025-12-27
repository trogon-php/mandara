@if($list_items)
    @foreach($list_items as $order)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="fw-bold">{{ $order->payment_order_id }}</div>
                <small class="text-muted">{{ $order->payment_method ?? 'N/A' }}</small>
            </td>
            <td>
                <div class="fw-bold">{{ $order->user->name ?? 'N/A' }}</div>
                <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
            </td>
            <td>
                <span class="badge bg-info">{{ $order->items->count() }} item(s)</span>
            </td>
            <td>
                <div class="fw-bold">₹{{ number_format($order->payable_amount, 2) }}</div>
                @if($order->discount_amount > 0)
                    <small class="text-muted text-decoration-line-through">₹{{ number_format($order->total_amount, 2) }}</small>
                    <small class="text-success">-₹{{ number_format($order->discount_amount, 2) }}</small>
                @endif
            </td>
            <td>
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    ];
                    $color = $statusColors[$order->order_status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $color }}">{{ ucfirst($order->order_status) }}</span>
            </td>
            <td>
                @php
                    $paymentColors = [
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'warning',
                    ];
                    $pColor = $paymentColors[$order->payment_status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $pColor }}">{{ ucfirst($order->payment_status) }}</span>
            </td>
            <td>
                <small>{{ $order->created_at->format('d-m-Y, g:i A') }}</small>
            </td>
            <td>
                @include('admin.crud.action-dropdown', [
                    'viewUrl'     => url('admin/estore-orders/'.$order->id),
                    'viewTitle'   => 'View Order Details',
                    'deleteUrl'   => null,
                    'redirectUrl' => route('admin.estore-orders.index')
                ])
            </td>
        </tr>
    @endforeach
@endif