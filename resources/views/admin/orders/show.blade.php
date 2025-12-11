@extends('admin.layouts.app')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $page_title }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url('admin/dashboard') }}" class="trogon-link">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('admin/orders') }}" class="trogon-link">Orders</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Order Details -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Order Information</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Order Number</label>
                            <p class="mb-0">{{ $order->order_number }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <p class="mb-0">
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
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Created Date</label>
                            <p class="mb-0">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Last Updated</label>
                            <p class="mb-0">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                @if($order->coupon_code)
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Coupon Code</label>
                            <p class="mb-0">
                                <span class="badge bg-info">{{ $order->coupon_code }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Customer Information</h4>
            </div>
            <div class="card-body">
                @if($order->user)
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name</label>
                            <p class="mb-0">{{ $order->user->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <p class="mb-0">{{ $order->user->email }}</p>
                        </div>
                    </div>
                </div>
                @if($order->user->phone)
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <p class="mb-0">{{ $order->user->phone }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @else
                <div class="alert alert-warning">
                    <i class="ri-alert-line"></i> Customer information not available
                </div>
                @endif
            </div>
        </div>

        <!-- Package Information -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Package Information</h4>
            </div>
            <div class="card-body">
                @if($order->package)
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Package Name</label>
                            <p class="mb-0">{{ $order->package->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Package Price</label>
                            <p class="mb-0">₹{{ number_format($order->package->price, 2) }}</p>
                        </div>
                    </div>
                </div>
                @if($order->package->description)
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <p class="mb-0">{{ $order->package->description }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @else
                <div class="alert alert-warning">
                    <i class="ri-alert-line"></i> Package information not available
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Order Summary</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Amount:</span>
                    <span class="fw-semibold">₹{{ number_format($order->amount_total, 2) }}</span>
                </div>
                
                @if($order->amount_offer > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount:</span>
                    <span class="text-success">-₹{{ number_format($order->amount_offer, 2) }}</span>
                </div>
                @endif
                
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Final Amount:</span>
                    <span class="fw-bold text-primary">₹{{ number_format($order->amount_final, 2) }}</span>
                </div>

                @if($order->amount_offer > 0)
                <div class="alert alert-success">
                    <i class="ri-discount-line"></i> 
                    You saved ₹{{ number_format($order->amount_offer, 2) }}!
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        @if($order->userPayments && $order->userPayments->count() > 0)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Payment History</h4>
            </div>
            <div class="card-body">
                @foreach($order->userPayments as $payment)
                <div class="border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Payment #{{ $payment->id }}</span>
                        <span class="fw-semibold">₹{{ number_format($payment->amount_paid, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $payment->created_at->format('M d, Y') }}</small>
                        <span class="badge bg-{{ $payment->payment_status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($payment->payment_status) }}
                        </span>
                    </div>
                    @if($payment->transaction_id)
                    <small class="text-muted">Transaction ID: {{ $payment->transaction_id }}</small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">
                        <i class="ri-edit-line"></i> Edit Order
                    </a>
                    
                    <button type="button" class="btn btn-warning" onclick="showStatusModal({{ $order->id }}, '{{ $order->status }}')">
                        <i class="ri-refresh-line"></i> Update Status
                    </button>
                    
                    <a href="{{ url('admin/orders') }}" class="btn btn-light">
                        <i class="ri-arrow-left-line"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showStatusModal(orderId, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    const form = document.getElementById('statusForm');
    const statusSelect = document.getElementById('status');
    
    form.action = `/admin/orders/${orderId}/update-status`;
    statusSelect.value = currentStatus;
    
    modal.show();
}

// Order stats function
function showOrderStats() {
    fetch('/admin/orders/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data;
                const message = `
                    <div class="row">
                        <div class="col-6"><strong>Total Orders:</strong> ${stats.total_orders}</div>
                        <div class="col-6"><strong>Pending:</strong> ${stats.pending_orders}</div>
                        <div class="col-6"><strong>Paid:</strong> ${stats.paid_orders}</div>
                        <div class="col-6"><strong>Cancelled:</strong> ${stats.cancelled_orders}</div>
                        <div class="col-12"><strong>Total Revenue:</strong> ₹${stats.total_revenue.toLocaleString()}</div>
                `;
                
                Swal.fire({
                    title: 'Order Statistics',
                    html: message,
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to load statistics', 'error');
        });
}

function filterByStatus(status) {
    const url = new URL(window.location);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}
</script>
@endsection


