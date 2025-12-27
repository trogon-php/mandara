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
                <div id="existing-booking-box" class="alert alert-warning d-none">
                    <h6 class="mb-2">Existing Booking Found</h6>
                    <div id="existing-booking-content"></div>
                </div>
                
                @include('admin.crud.form', [
                    'action' => route('admin.mandara-bookings.store'),
                    'formId' => 'add-mandara-booking-form',
                    'submitText' => 'Reserve My Stay',
                    'fields' => [
                       
                        [
                            'type'=>'text',
                            'name'=>'phone',
                            'label'=>'Phone',
                            'placeholder'=>'Enter the phone number of the client',
                            'required'=>false,
                            'col'=>12
                        ],
                        [
                            'type' => 'text',
                            'name' => 'email',
                            'id' => 'email',
                            'label' => 'Email',
                            'placeholder' => 'Enter the email of the client',
                            'required' => false,
                            'col' => 12
                        ],
                        [
                            'type' => 'select',
                            'name' => 'is_delivered',
                            'id' => 'is_delivered',
                            'label' => 'Have you delivered your baby?',
                            'placeholder' => 'Select the delivery status',
                            'options' => [
                                1 => 'Delivered',
                                0 => 'Expected',
                            ],
                            'col' => 12
                        ],
                        [
                            'type' => 'date',
                            'name' => 'delivery_date',
                            'id' => 'delivery_date',
                            'label' => 'Delivery Date',
                            'placeholder' => 'Select the delivery date',
                            'required' => false,
                            'col' => 12
                        ],
                        [
                            'type' => 'date',
                            'name' => 'date_from',
                            'id' => 'date_from',
                            'label' => 'Arrival Date',
                            'placeholder' => 'Select the arrival date',
                            'required' => false,
                            'col' => 6
                        ],
                        [
                            'type' => 'date',
                            'name' => 'date_to',
                            'id' => 'date_to',
                            'label' => 'Departure Date',
                            'placeholder' => 'Select the departure date',
                            'required' => false,
                            'col' => 6
                        ],
                        [
                            'type' => 'textarea',
                            'name' => 'additional_note',
                            'id' => 'additional_note',
                            'label' => 'Additional Note',
                            'placeholder' => 'Enter the additional note',
                            'required' => false,
                            'col' => 6
                        ],
                
                    ]
                ])

                

               
            </div>
        </div>
    </div>
</div>
<script>
   
let debounceTimer = null;

// helper: fill field only if empty
function setIfEmpty(selector, value) {
    const el = document.querySelector(selector);
    if (!el || el.value) return;
    el.value = value;
}

// helper: set select value safely
function setSelect(selector, valueMap, value) {
    const el = document.querySelector(selector);
    if (!el) return;

    const mapped = valueMap[value];
    if (mapped !== undefined) {
        el.value = mapped;
        el.dispatchEvent(new Event('change'));
    }
}

function fillFormFields(data) {
    // text fields
    if (data.phone) setIfEmpty('input[name="phone"]', data.phone);
    if (data.email) setIfEmpty('input[name="email"]', data.email);

    // delivery status select
    if (data.delivery_status) {
        setSelect(
            'select[name="is_delivered"]',
            { 'Delivered': '1', 'Expected': '0' },
            data.delivery_status
        );
    }

    // date fields
    if (data.delivery_date) setIfEmpty('input[name="delivery_date"]', data.delivery_date);
    if (data.arrival_date) setIfEmpty('input[name="date_from"]', data.arrival_date);
    if (data.departure_date) setIfEmpty('input[name="date_to"]', data.departure_date);

    // textarea
    if (data.additional_note) {
        const ta = document.querySelector('textarea[name="additional_note"]');
        if (ta && !ta.value) ta.value = data.additional_note;
    }
}


function checkExistingBooking() {
    const phone = document.querySelector('[name="phone"]')?.value || '';
    const email = document.querySelector('[name="email"]')?.value || '';

    if (phone.length < 4 && email.length < 4) {
        hideExistingBox();
        return;
    }

    fetch(`{{ route('admin.mandara-bookings.check-existing') }}?phone=${encodeURIComponent(phone)}&email=${encodeURIComponent(email)}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 1) {
                fillFormFields(res.data);   // 🔥 AUTO-FILL
                showExistingBox(res.data);  // (optional preview)
            } else {
                hideExistingBox();
            }
        });
}

    function fillHiddenFields(data) {
    if (data.user_id) {
        document.querySelector('[name="user_id"]').value = data.user_id;
    }
    if (data.booking_number) {
        document.querySelector('[name="booking_number"]').value = data.booking_number;
    }

}


document.addEventListener('input', function (e) {
    if (e.target.name === 'phone' || e.target.name === 'email') {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(checkExistingBooking, 400);
    }
});




    
</script>

@endsection
