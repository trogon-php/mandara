@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('admin/payments') }}">Payments</a></li>
                        <li class="breadcrumb-item active">Payment Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Payment Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Payment Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Payment ID</label>
                                <p class="form-control-plaintext">{{ $payment->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Transaction ID</label>
                                <p class="form-control-plaintext">{{ $payment->transaction_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Payment Status</label>
                                <p class="form-control-plaintext">
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
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created Date</label>
                                <p class="form-control-plaintext">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Amount Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Total Amount</label>
                                <p class="form-control-plaintext h5 text-primary">₹{{ number_format($payment->amount_total, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Amount Paid</label>
                                <p class="form-control-plaintext h5 text-success">₹{{ number_format($payment->amount_paid, 2) }}</p>
                            </div>
                        </div>
                        @if($payment->amount_total != $payment->amount_paid)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Outstanding Amount</label>
                                <p class="form-control-plaintext h5 text-warning">₹{{ number_format($payment->amount_total - $payment->amount_paid, 2) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            @if($payment->remarks)
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Remarks</h4>
                </div>
                <div class="card-body">
                    <p class="form-control-plaintext">{{ $payment->remarks }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Information -->
        <div class="col-md-4">
            <!-- User Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">User Information</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name</label>
                        <p class="form-control-plaintext">{{ $payment->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <p class="form-control-plaintext">{{ $payment->user->email ?? 'N/A' }}</p>
                    </div>
                    @if($payment->user->phone)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone</label>
                        <p class="form-control-plaintext">{{ $payment->user->phone }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Order Information</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Order Number</label>
                        <p class="form-control-plaintext">{{ $payment->order->order_number ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Order Status</label>
                        <p class="form-control-plaintext">
                            @if($payment->order)
                                @php
                                    $orderStatusColors = [
                                        'pending' => 'warning',
                                        'partially_paid' => 'info',
                                        'paid' => 'success',
                                        'cancelled' => 'danger',
                                        'refunded' => 'secondary'
                                    ];
                                    $orderStatusColor = $orderStatusColors[$payment->order->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $orderStatusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->order->status)) }}
                                </span>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Package Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Package Information</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Package Name</label>
                        <p class="form-control-plaintext">{{ $payment->package->title ?? 'N/A' }}</p>
                    </div>
                    @if($payment->package)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Package Price</label>
                        <p class="form-control-plaintext">₹{{ number_format($payment->package->price, 2) }}</p>
                    </div>
                    @if($payment->package->offer_price)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Offer Price</label>
                        <p class="form-control-plaintext text-success">₹{{ number_format($payment->package->offer_price, 2) }}</p>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn btn-primary">
                            <i class="ri-edit-line"></i> Edit Payment
                        </a>
                        
                        <button type="button" class="btn btn-warning" onclick="showStatusModal({{ $payment->id }}, '{{ $payment->payment_status }}')">
                            <i class="ri-refresh-line"></i> Update Status
                        </button>
                        
                        <a href="{{ url('admin/payments') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line"></i> Back to Payments
                        </a>
                    </div>
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
                <h5 class="modal-title" id="statusModalLabel">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_status" class="form-label">New Status</label>
                        <select class="form-select" id="new_status" name="payment_status" required>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showStatusModal(paymentId, currentStatus) {
    document.getElementById('new_status').value = currentStatus;
    document.getElementById('statusUpdateForm').action = `/admin/payments/${paymentId}/update-status`;
    
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const paymentId = this.action.split('/').pop();
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while updating the status', 'error');
    });
});
</script>
@endsection
