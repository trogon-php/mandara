@include('admin.crud.crud-index-layout', [
    'page_title'    => 'Orders',
    'createUrl'     => url('admin/orders/create'),
    'redirectUrl'   => url('admin/orders'),
    'tableId'       => 'orders-table',
    'list_items'    => $list_items,
    'breadcrumbs'   => [
        'Dashboard' => url('admin/dashboard'),
        'Orders'    => null,
    ],
    'filters'       => view('admin.partials.universal-filters', [
        'filterConfig' => $filterConfig,
        'searchConfig' => $searchConfig
    ]),
    'customButtons' => [
        [
            'type'  => 'button',
            'text'  => 'Order Stats',
            'class' => 'btn-outline-info',
            'icon'  => 'mdi mdi-chart-line',
            'onclick' => 'showOrderStats()',
        ],
        [
            'type'  => 'button',
            'text'  => 'Pending Orders',
            'class' => 'btn-outline-warning',
            'icon'  => 'mdi mdi-clock-outline',
            'onclick' => 'filterByStatus("pending")',
        ],
        [
            'type'  => 'button',
            'text'  => 'Paid Orders',
            'class' => 'btn-outline-success',
            'icon'  => 'mdi mdi-check-circle',
            'onclick' => 'filterByStatus("paid")',
        ]
    ],
    'tableHead'     => '
        <tr>
            <th>Order Details</th>
            <th>User</th>
            <th>Package</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    ',
    'tableBody'     => view('admin.orders.index-table', compact('list_items'))
])

<script>
// Define the order form initialization function globally
function initializeOrderForm() {
    console.log('Initializing order form...');

    // Auto-calculate amounts when package changes
    $('#package_id').on('change', function() {
        const packageId = $(this).val();
        console.log('Package changed, ID:', packageId);
        if (packageId) {
            console.log('Fetching package details for ID:', packageId);
            // Fetch package details and calculate amounts
            fetch(`/admin/packages/${packageId}/details`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    console.log('Package details response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Package details data:', data);
                    if (data.success) {
                        // Use offer_price if available, otherwise use price
                        const packagePrice = data.package.offer_price || data.package.price;
                        console.log('Setting package price:', packagePrice);
                        $('#amount_total').val(packagePrice);
                        $('#amount_final').val(packagePrice);
                        $('#amount_offer').val('0.00');
                    } else {
                        console.error('Package fetch failed:', data.message);
                        alert('Failed to fetch package details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error fetching package details:', error);
                    alert('Error fetching package details: ' + error.message);
                });
        } else {
            // Reset amounts
            console.log('No package selected, resetting amounts');
            $('#amount_total').val('');
            $('#amount_final').val('');
            $('#amount_offer').val('');
        }
    });

    // Auto-calculate amounts when coupon code changes
    $('#coupon_code').on('input', function() {
        const couponCode = $(this).val();
        const packageId = $('#package_id').val();
        
        if (couponCode && packageId) {
            // Fetch coupon details and calculate discount
            fetch(`/admin/coupons/validate/${couponCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.coupon) {
                        const totalAmount = parseFloat($('#amount_total').val()) || 0;
                        let discount = 0;
                        
                        if (data.coupon.discount_type === 'percentage') {
                            discount = (totalAmount * data.coupon.discount_value) / 100;
                        } else {
                            discount = data.coupon.discount_value;
                        }
                        
                        const finalAmount = Math.max(0, totalAmount - discount);
                        
                        $('#amount_offer').val(discount.toFixed(2));
                        $('#amount_final').val(finalAmount.toFixed(2));
                        $('#coupon_id').val(data.coupon.id);
                    } else {
                        // Reset discount if coupon is invalid
                        const totalAmount = parseFloat($('#amount_total').val()) || 0;
                        $('#amount_offer').val('0.00');
                        $('#amount_final').val(totalAmount.toFixed(2));
                        $('#coupon_id').val('');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reset discount on error
                    const totalAmount = parseFloat($('#amount_total').val()) || 0;
                    $('#amount_offer').val('0.00');
                    $('#amount_final').val(totalAmount.toFixed(2));
                    $('#coupon_id').val('');
                });
        } else {
            // Reset discount if no coupon code
            const totalAmount = parseFloat($('#amount_total').val()) || 0;
            $('#amount_offer').val('0.00');
            $('#amount_final').val(totalAmount.toFixed(2));
            $('#coupon_id').val('');
        }
    });
}

// Function to show status update modal
function showStatusModal(orderId, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    const form = document.getElementById('statusForm');
    const statusSelect = document.getElementById('status');
    
    form.action = `/admin/orders/${orderId}/update-status`;
    statusSelect.value = currentStatus;
    
    modal.show();
}

// Function to view payments for an order
function viewOrderPayments(orderId) {
    showAjaxModal(`/admin/orders/${orderId}/payments`, 'Order Payments', function(response) {
        // This callback will be called after the modal content is loaded
        console.log('Payments modal loaded');
    });
}

// Override the create button to use callback
// $(document).ready(function() {
//     // Override the create button click to use callback
//     $('button[onclick*="showAjaxModal"][onclick*="admin/orders/create"]').attr('onclick', 'showAjaxModal("{{ url('admin/orders/create') }}", "Add New Order")');
// });
</script>

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
                        <select class="form-select select2" id="update_status" name="status" required>
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
