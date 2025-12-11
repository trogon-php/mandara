@include('admin.crud.form', [
    'action' => route('admin.orders.store'),
    'formId' => 'add-order-form',
    'submitText' => 'Create Order',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.orders.index'),
    'fields' => [
        [
            'type' => 'select2-ajax',
            'name' => 'user_id',
            'id' => 'user_id',
            'label' => 'Client',
            'placeholder' => 'Select a client',
            'per_page' => 15,
            'required' => true,
            'ajax_url' => route('admin.clients.select2-ajax-options'),
            // 'options' => $users,
            'col' => 6
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
            'type' => 'text',
            'name' => 'coupon_code',
            'id' => 'coupon_code',
            'label' => 'Coupon Code',
            'placeholder' => 'Enter coupon code (optional)',
            'col' => 6
        ],
        [
            'type' => 'hidden',
            'name' => 'coupon_id',
            'id' => 'coupon_id',
            'value' => ''
        ],
        [
            'type' => 'number',
            'name' => 'amount_total',
            'id' => 'amount_total',
            'label' => 'Total Amount',
            'placeholder' => '0.00',
            'step' => '0.01',
            'required' => true,
            'readonly' => true,
            'col' => 4
        ],
        [
            'type' => 'number',
            'name' => 'amount_offer',
            'id' => 'amount_offer',
            'label' => 'Discount Amount',
            'placeholder' => '0.00',
            'step' => '0.01',
            'readonly' => true,
            'col' => 4
        ],
        [
            'type' => 'number',
            'name' => 'amount_final',
            'id' => 'amount_final',
            'label' => 'Final Amount',
            'placeholder' => '0.00',
            'step' => '0.01',
            'required' => true,
            'readonly' => true,
            'col' => 4
        ],
        [
            'type' => 'text',
            'name' => 'order_number',
            'id' => 'order_number',
            'label' => 'Order Number',
            'placeholder' => 'Leave empty for auto-generation',
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'value' => 'paid',
            'options' => [
                'pending' => 'Pending',
                'partially_paid' => 'Partially Paid',
                'paid' => 'Paid',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded'
            ],
            'col' => 6
        ],
    ]
])

<script>
    
</script>
