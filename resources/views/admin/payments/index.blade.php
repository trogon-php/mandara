@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Payments',
    'createUrl'     => url('admin/payments/create'),
    'redirectUrl'   => url('admin/payments'),
    'tableId'       => 'payments-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Payments'    => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'button',
            'text'  => 'Payment Stats',
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-chart-line',
            'onclick' => 'showPaymentStats()',
        ],
        [
            'type'  => 'button',
            'text'  => 'Pending Payments',
            'class' => 'btn-outline-warning',
            'icon'  => 'mdi mdi-clock-outline',
            'onclick' => 'filterByStatus("pending")',
        ],
        [
            'type'  => 'button',
            'text'  => 'Paid Payments',
            'class' => 'btn-outline-success',
            'icon'  => 'mdi mdi-check-circle',
            'onclick' => 'filterByStatus("paid")',
        ]
    ],
    'tableHead'     => '
        <tr>
            <th>Payment Details</th>
            <th>User</th>
            <th>Order</th>
            <th>Package</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.payments.index-table', compact('list_items'))
])

<script>
// Payment statistics function
function showPaymentStats() {
    fetch('/admin/payments/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data;
                const statsHtml = `
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Payments</h5>
                                    <h3>${stats.total_payments}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Paid</h5>
                                    <h3>${stats.paid_payments}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Pending</h5>
                                    <h3>${stats.pending_payments}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5>Failed</h5>
                                    <h3>${stats.failed_payments}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Total Amount Collected</h5>
                                    <h3>₹${parseFloat(stats.total_amount).toFixed(2)}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <h5>Pending Amount</h5>
                                    <h3>₹${parseFloat(stats.pending_amount).toFixed(2)}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                Swal.fire({
                    title: 'Payment Statistics',
                    html: statsHtml,
                    width: '800px',
                    showCloseButton: true,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to load payment statistics', 'error');
        });
}

// Function to show payment status update modal
function showPaymentStatusModal(paymentId, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('paymentStatusModal'));
    const form = document.getElementById('paymentStatusForm');
    const statusSelect = document.getElementById('payment_status');
    
    form.action = `/admin/payments/${paymentId}/update-status`;
    statusSelect.value = currentStatus;
    
    modal.show();
}

// Filter by status function
function filterByStatus(status) {
    // Add status filter to the form and submit
    const filterForm = document.querySelector('form[action*="admin/payments"]');
    if (filterForm) {
        // Remove existing status filter
        const existingStatusFilter = filterForm.querySelector('select[name="payment_status"]');
        if (existingStatusFilter) {
            existingStatusFilter.value = status;
        } else {
            // Create hidden input for status filter
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'payment_status';
            statusInput.value = status;
            filterForm.appendChild(statusInput);
        }
        filterForm.submit();
    }
}
</script>

<!-- Payment Status Update Modal -->
<div class="modal fade" id="paymentStatusModal" tabindex="-1" aria-labelledby="paymentStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentStatusModalLabel">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentStatusForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">New Status</label>
                        <select class="form-select" id="payment_status" name="payment_status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
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
