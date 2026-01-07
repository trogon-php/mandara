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
                @php
                    $currentAssignment = $order->currentAssignment;
                    $hasAssignment = $currentAssignment !== null;
                @endphp
                
                <div class="d-flex align-items-center gap-1">
                    @if($hasAssignment)
                        {{-- <span class="badge bg-success-subtle text-success">
                            <i class="ri-user-line me-1"></i>
                            {{ $currentAssignment->deliveryStaff->name ?? 'Assigned' }}
                        </span>
                        @if($currentAssignment->delivery_room)
                            <span class="badge bg-info-subtle text-info">
                                <i class="ri-home-line me-1"></i>
                                Room {{ $currentAssignment->delivery_room }}
                            </span>
                        @endif --}}
                        {{-- REASSIGN BUTTON --}}
                        <button type="button"
                            class="btn btn-sm btn-warning waves-effect waves-light"
                            onclick="openAssignModal(
                                {{ $order->id }},
                                '{{ $order->payment_order_id }}',
                                '{{ $order->user->name ?? 'N/A' }}',
                                {{ $currentAssignment->delivery_staff_id }}
                            )">
                            <i class="ri-refresh-line me-1"></i> Reassign
                        </button>
                    @else
                        <button type="button" 
                                class="btn btn-sm btn-primary waves-effect waves-light"
                                onclick="openAssignModal({{ $order->id }},
                                 '{{ $order->payment_order_id }}',
                                  '{{ $order->user->name ?? 'N/A' }}')">
                            <i class="ri-user-add-line me-1"></i> Assign
                        </button>
                    @endif
                    
                    @include('admin.crud.action-dropdown', [
                        'viewUrl'     => url('admin/estore-orders/'.$order->id),
                        'viewTitle'   => 'View Order Details',
                        'deleteUrl'   => null,
                        'redirectUrl' => route('admin.estore-orders.index')
                    ])
                </div>
            </td>
        </tr>
    @endforeach
@endif