@include('admin.crud.form', [
    'action' => route('admin.orders.update', $edit_data->id),
    'method' => 'PUT',
    'formId' => 'edit-order-form',
    'submitText' => 'Update Order',
    'class' => 'ajax-crud-form',
    'redirect' => route('admin.orders.index'),
    'fields' => [
        [
            'type' => 'select2-ajax',
            'name' => 'user_id',
            'id' => 'user_id',
            'label' => 'Student',
            'required' => true,
            // 'options' => $users,
            'ajax_url' => route('admin.students.select2-ajax-options'),
            'default' => [
                'key' => $edit_data->user_id,
                'label' => $edit_data->user->name . ' [+' .$edit_data->user->country_code . ' ' . $edit_data->user->phone . ']'
            ],
            'col' => 6
        ],
        [
            'type' => 'select2',
            'name' => 'package_id',
            'id' => 'package_id',
            'label' => 'Package',
            'required' => true,
            'options' => $packages,
            'value' => old('package_id', $edit_data->package_id),
            'col' => 6
        ],
        [
            'type' => 'text',
            'name' => 'coupon_code',
            'id' => 'coupon_code',
            'label' => 'Coupon Code',
            'placeholder' => 'Enter coupon code (optional)',
            'value' => old('coupon_code', $edit_data->coupon_code),
            'col' => 6
        ],
        [
            'type' => 'hidden',
            'name' => 'coupon_id',
            'id' => 'coupon_id',
            'value' => old('coupon_id', $edit_data->coupon_id)
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
            'value' => old('amount_total', $edit_data->amount_total),
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
            'value' => old('amount_offer', $edit_data->amount_offer),
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
            'value' => old('amount_final', $edit_data->amount_final),
            'col' => 4
        ],
        [
            'type' => 'text',
            'name' => 'order_number',
            'id' => 'order_number',
            'label' => 'Order Number',
            'placeholder' => 'Order number',
            'value' => old('order_number', $edit_data->order_number),
            'col' => 6
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'options' => [
                'pending' => 'Pending',
                'partially_paid' => 'Partially Paid',
                'paid' => 'Paid',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded'
            ],
            'value' => old('status', $edit_data->status),
            'col' => 6
        ],
    ]
])

<script>
// Initialize when DOM is ready (for direct page access)
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initializeOrderForm === 'function') {
        initializeOrderForm();
    }
});
</script>
