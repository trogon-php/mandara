@extends('admin.layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $page_title }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @foreach($breadcrumbs ?? [] as $label => $url)
                        @if($loop->last || empty($url))
                            <li class="breadcrumb-item active">{{ $label }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $url }}" class="trogon-link">{{ $label }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Card -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <a class="btn btn-md btn-outline-dark rounded-pill trogon-link"
                   href="{{ route('admin.mandara-bookings.index') }}">
                    <i class="mdi mdi-arrow-left"></i> Back to Mandara Bookings
                </a>
            </div>

            <div class="card-body">

                @php
                    $isPaid = !empty($paymentOrder);
                @endphp

                {{-- PAYMENT SUMMARY --}}
                <div class="mb-4">
                    <h6>Payment Summary</h6>

                    <p>
                        Package Amount:
                        <strong>₹ {{ number_format($bookingAmount, 2) }}</strong>
                    </p>

                    @if($taxPercent > 0)
                        <p>
                            GST ({{ $taxPercent }}%):
                            <strong>₹ {{ number_format($taxAmount, 2) }}</strong>
                        </p>
                    @endif

                    <hr>

                    <p>
                        Total Payable:
                        <strong>₹ {{ number_format($totalAmount, 2) }}</strong>
                    </p>
                </div>

               
                {{-- CASE 1: PAYMENT DONE --}}
               
                @if($isPaid)

                    <div class="alert alert-success">
                        Payment already completed on
                        {{ $paymentOrder->created_at->format('d M Y, h:i A') }}
                    </div>

                    @include('admin.crud.form', [
                        'action' => '#',
                        'disableSubmit' => true,
                        'fields' => [
                            [
                                'type' => 'select',
                                'name' => 'payment_method',
                                'label' => 'Payment Method',
                                'options' => [
                                    'cash'   => 'Cash',
                                    'online' => 'Online',
                                    'bank'   => 'Bank',
                                ],
                                'value' => $paymentOrder->payment_method,
                                'disabled' => true,
                                'col' => 6
                            ],
                            [
                                'type' => 'text',
                                'name' => 'payment_id',
                                'label' => 'Transaction ID',
                                'value' => $paymentOrder->payment_id,
                                'disabled' => true,
                                'col' => 6
                            ],
                        ]
                    ])

                    <a href="{{ route('admin.mandara-bookings.additional-details', $booking->id) }}"
                       class="btn btn-primary mt-3">
                        Next
                    </a>

                @endif

                
                {{-- CASE 2: PAYMENT PENDING --}}
              
                @if(!$isPaid)

                    @include('admin.crud.form', [
                        'action' => route('admin.mandara-bookings.payment-store', $booking->id),
                        'submitText' => 'Confirm Payment',
                        'fields' => [
                            [
                                'type' => 'hidden',
                                'name' => 'payable_amount',
                                'value' => $totalAmount
                            ],
                            [
                                'type' => 'select',
                                'name' => 'payment_method',
                                'id' => 'payment_method',
                                'label' => 'Payment Method',
                                'placeholder' => 'Select payment method',
                                'options' => [
                                    'cash'   => 'Cash',
                                    'online' => 'Online',
                                    'bank'   => 'Bank',
                                ],
                                'required' => true,
                                'col' => 6
                            ],
                            [
                                'type' => 'text',
                                'name' => 'payment_id',
                                'id' => 'payment_id',
                                'label' => 'Transaction ID (Bank only)',
                                'required' => false,
                                'col' => 6
                            ],
                        ]
                    ])

                @endif

            </div>
        </div>
    </div>
</div>


<script>
/**
 * Toggle transaction ID field
 */
document.addEventListener('change', function (e) {
    if (e.target.name !== 'payment_method') return;

    const txnBox = document.getElementById('payment_id')?.closest('.col-md-12');
    if (!txnBox) return;

    if (e.target.value === 'bank') {
        txnBox.style.display = 'block';
    } else {
        txnBox.style.display = 'none';
        document.getElementById('payment_id').value = '';
    }
});

/**
 * Force-select payment method (cannot modify common CRUD blade)
 */
document.addEventListener('DOMContentLoaded', function () {
    const paymentMethod = @json($paymentOrder?->payment_method);

    if (!paymentMethod) return;

    const select = document.querySelector('select[name="payment_method"]');
    if (!select) return;

    // Force correct selection
    select.value = paymentMethod;
    select.dispatchEvent(new Event('change', { bubbles: true }));
});
</script>

@endsection
