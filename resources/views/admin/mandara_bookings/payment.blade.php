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
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <a class="btn btn-md btn-outline-dark rounded-pill float-start trogon-link me-2 mt-2"
                        href="{{ route('admin.mandara-bookings.index') }}">
                         <i class="mdi mdi-arrow-left"></i>
                         Back to Mandara Bookings
                     </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                
                @include('admin.crud.form', [
                    'action' => route('admin.mandara-bookings.payment-store', $booking->id),
                    'formId' => 'add-mandara-booking-form',
                    'submitText' => 'Confirm Payment',
                    'fields' => [
                       
                       
                        [
                            'type' => 'select',
                            'name' => 'payment_mode',
                            'id' => 'payment_mode',
                            'label' => 'Payment Mode',
                            'placeholder' => 'Select the payment mode',
                            'options' => [
                                'cash' => 'Cash',
                                'online' => 'Online',
                                'bank' => 'Bank',
                            ],
                            'col' => 12
                        ],
                        [
                            'type' => 'number',
                            'name' => 'payable_amount',
                            'id' => 'payable_amount',
                            'label' => 'Payable Amount',
                            'placeholder' => 'Enter the payable amount',
                            'required' => false,
                            'col' => 12
                        ], 
                
                    ]
                ])
               
            </div>
        </div>
    </div>
</div>


@endsection
