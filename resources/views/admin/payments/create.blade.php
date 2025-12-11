@include('admin.crud.form', [
    'action' => route('admin.payments.store'),
    'formId' => 'add-payment-form',
    'submitText' => 'Create Payment',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.payments.index'),
    'fields' => [
        [
            'type' => 'select2',
            'name' => 'order_id',
            'id' => 'order_id',
            'label' => 'Order',
            'required' => true,
            'options' => $orders,
            'col' => 6
        ],
        [
            'type' => 'hidden',
            'name' => 'user_id',
            'id' => 'user_id',
            'value' => ''
        ],
        [
            'type' => 'select2',
            'name' => 'package_id',
            'id' => 'package_id',
            'label' => 'Package',
            'required' => true,
            'options' => $packages,
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'payment_status',
            'id' => 'payment_status',
            'label' => 'Payment Status',
            'required' => true,
            'value' => 'paid',
            'options' => [
                'pending' => 'Pending',
                'paid' => 'Paid',
                'failed' => 'Failed',
                'refunded' => 'Refunded'
            ],
            'col' => 6
        ],
        [
            'type' => 'number',
            'name' => 'amount_total',
            'id' => 'amount_total',
            'label' => 'Total Amount',
            'placeholder' => '0.00',
            'step' => '0.01',
            'required' => true,
            'col' => 4
        ],
        [
            'type' => 'number',
            'name' => 'amount_paid',
            'id' => 'amount_paid',
            'label' => 'Amount Paid',
            'placeholder' => '0.00',
            'step' => '0.01',
            'required' => true,
            'col' => 4
        ],
        [
            'type' => 'text',
            'name' => 'transaction_id',
            'id' => 'transaction_id',
            'label' => 'Transaction ID',
            'placeholder' => 'Enter transaction ID (optional)',
            'col' => 4
        ],
        [
            'type' => 'textarea',
            'name' => 'remarks',
            'id' => 'remarks',
            'label' => 'Remarks',
            'placeholder' => 'Enter any remarks (optional)',
            'rows' => 3,
            'col' => 12
        ],
    ]
])

<script>
// Initialize when DOM is ready (for direct page access)
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initializePaymentForm === 'function') {
        initializePaymentForm();
    }
});

// Define the payment form initialization function globally
function initializePaymentForm() {
    console.log('Initializing payment form...');
    
    // Initialize Select2 for orders
    $('#order_id').select2({
        placeholder: 'Select Order',
        allowClear: true,
        width: '100%'
    });

    // Initialize Select2 for packages
    $('#package_id').select2({
        placeholder: 'Select Package',
        allowClear: true,
        width: '100%'
    });

    // Auto-populate fields when order changes
    $('#order_id').on('change select2:select', function() {
        const orderId = $(this).val();
        console.log('Order changed, ID:', orderId);
        
        if (orderId) {
            // Fetch order details and populate fields
            fetch(`/admin/orders/${orderId}/details`)
                .then(response => response.json())
                .then(data => {
                    console.log('Order data received:', data);
                    if (data.success && data.order) {
                        const order = data.order;
                        console.log('Setting user_id to:', order.user_id);
                        $('#user_id').val(order.user_id);
                        $('#package_id').val(order.package_id).trigger('change');
                        $('#amount_total').val(order.amount_final);
                        $('#amount_paid').val(order.amount_final);
                        
                        // Verify the user_id was set
                        console.log('user_id field value after setting:', $('#user_id').val());
                    }
                })
                .catch(error => {
                    console.error('Error fetching order details:', error);
                });
        } else {
            // Reset fields
            $('#user_id').val('');
            $('#package_id').val('').trigger('change');
            $('#amount_total').val('');
            $('#amount_paid').val('');
        }
    });

    // Add form submission validation
    $('#add-payment-form').on('submit', function(e) {
        const userId = $('#user_id').val();
        console.log('Form submitting with user_id:', userId);
        
        if (!userId) {
            e.preventDefault();
            alert('Please select an order first to auto-populate the student information.');
            return false;
        }
    });
}
</script>
