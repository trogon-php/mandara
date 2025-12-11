@include('admin.crud.form', [
    'action' => route('admin.coupons.update', $coupon->id),
    'method' => 'PUT',
    'formId' => 'edit-coupon-form',
    'submitText' => 'Update Coupon',
    'class'      => 'ajax-crud-form',
    'redirect'   => route('admin.coupons.index'),
    'fields' => [
        [
            'type' => 'text',
            'name' => 'code',
            'id' => 'code',
            'label' => 'Coupon Code',
            'placeholder' => 'Enter unique coupon code',
            'required' => true,
            'col' => 6,
            'value' => $coupon->code,
            'help' => 'This will be the code users enter during checkout'
        ],
        [
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => 'Coupon Title',
            'placeholder' => 'Enter coupon title',
            'required' => true,
            'col' => 6,
            'value' => $coupon->title
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'id' => 'description',
            'label' => 'Description',
            'placeholder' => 'Enter coupon description',
            'col' => 12,
            'value' => $coupon->description
        ],
        [
            'type' => 'select',
            'name' => 'discount_type',
            'id' => 'discount_type',
            'label' => 'Discount Type',
            'required' => true,
            'col' => 6,
            'value' => $coupon->discount_type,
            'options' => [
                'percentage' => 'Percentage (%)',
                'fixed' => 'Fixed Amount (₹)'
            ]
        ],
        [
            'type' => 'number',
            'name' => 'discount_value',
            'id' => 'discount_value',
            'label' => 'Discount Value',
            'placeholder' => 'Enter discount value',
            'required' => true,
            'step' => '0.01',
            'min' => '0',
            'col' => 6,
            'value' => $coupon->discount_value,
            'help' => 'For percentage: 0-100, For fixed: amount in ₹'
        ],
        [
            'type' => 'date',
            'name' => 'start_date',
            'id' => 'start_date',
            'label' => 'Start Date',
            'required' => true,
            'col' => 6,
            'value' => $coupon->start_date->format('Y-m-d')
        ],
        [
            'type' => 'date',
            'name' => 'end_date',
            'id' => 'end_date',
            'label' => 'End Date',
            'required' => true,
            'col' => 6,
            'value' => $coupon->end_date->format('Y-m-d')
        ],
        [
            'type' => 'number',
            'name' => 'usage_limit',
            'id' => 'usage_limit',
            'label' => 'Usage Limit (Optional)',
            'placeholder' => 'Leave empty for unlimited',
            'min' => '1',
            'col' => 6,
            'value' => $coupon->usage_limit,
            'help' => 'Maximum number of times this coupon can be used'
        ],
        [
            'type' => 'number',
            'name' => 'per_user_limit',
            'id' => 'per_user_limit',
            'label' => 'Per User Limit (Optional)',
            'placeholder' => 'Leave empty for unlimited',
            'min' => '1',
            'col' => 6,
            'value' => $coupon->per_user_limit,
            'help' => 'Maximum number of times a single user can use this coupon'
        ],
        [
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'label' => 'Status',
            'required' => true,
            'col' => 6,
            'value' => $coupon->status,
            'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive'
            ]
        ],
        [
            'type' => 'select-multiple',
            'name' => 'package_ids',
            'id' => 'package_ids',
            'label' => 'Applicable Packages (Optional)',
            'col' => 12,
            'select2' => true,
            'value' => $coupon->packages->pluck('id')->toArray(),
            'options' => $packages->pluck('title', 'id')->toArray(),
            'help' => 'Leave empty to apply to all packages'
        ],
        [
            'type' => 'select-multiple',
            'name' => 'user_ids',
            'id' => 'user_ids',
            'label' => 'Restricted Users (Optional)',
            'col' => 12,
            'select2' => true,
            'value' => $coupon->users->pluck('id')->toArray(),
            'options' => $users->mapWithKeys(function($user) {
                $phone = $user->phone ? " [+{$user->country_code} {$user->phone}]" : '';
                return [$user->id => $user->name . $phone];
            })->toArray(),
            'help' => 'Leave empty to allow all users'
        ]
    ],
    'breadcrumbs' => [
        'Dashboard' => url('admin/dashboard'),
        'Coupons' => route('admin.coupons.index'),
        'Edit Coupon' => null,
    ]
])
