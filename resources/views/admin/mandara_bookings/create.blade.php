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

                {{-- STATUS --}}
                <div id="client-message" class="alert alert-warning d-none"></div>

                {{-- SINGLE FORM --}}
                <form id="mandara-booking-form"
                    method="POST"
                    action="{{ route('admin.mandara-bookings.store') }}">
                    @csrf
                    @if(isset($prefillBooking))
                        <input type="hidden" name="booking_id" value="{{ $prefillBooking->id }}">
                    @endif

                    {{-- ================= STEP 1 : CLIENT ================= --}}
                    <h5>Client Details</h5>
                    <div class="row">

                        <div class="col-md-3">
                            @include('admin.crud.fields.country-code', [
                                'name' => 'country_code',
                                'label' => 'Country Code',
                                'value' => old('country_code', $prefillBooking->user->country_code ?? '')
                            ])
                        </div>

                        <div class="col-md-6">
                            @include('admin.crud.fields.text', [
                                'name' => 'phone',
                                'label' => 'Phone',
                                'value' => old('phone', $prefillBooking->user->phone ?? '')
                            ])
                        </div>

                        <div class="col-md-6">
                            @include('admin.crud.fields.text', [
                                'name' => 'email',
                                'label' => 'Email',
                                'value' => old('email', $prefillBooking->user->email ?? '')
                            ])
                        </div>

                        <div class="col-md-12">
                            @include('admin.crud.fields.text', [
                                'name' => 'name',
                                'label' => 'Full Name',
                                'required' => true,
                                'value' => old('phone', $prefillBooking->user->phone ?? '')
                            ])
                        </div>
                    </div>

                    <button type="button"
                            id="next-btn"
                            class="btn btn-primary mt-3 d-none">
                        Next
                    </button>

                    <hr>

                    {{-- ================= STEP 2 : BOOKING ================= --}}
                    <div id="booking-section" class="d-none">

                        <h5>Booking Details</h5>

                        <div class="row">

                            <div class="col-md-6">
                                @include('admin.crud.fields.select', [
                                    'name' => 'cottage_package_id',
                                    'label' => 'Cottage Package',
                                    'options' => $cottagePackages,
                                    'required' => true
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('admin.crud.fields.select', [
                                    'name' => 'is_delivered',
                                    'label' => 'Have you delivered your baby?',
                                    'options' => [1 => 'Delivered', 0 => 'Expected']
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('admin.crud.fields.date', [
                                    'name' => 'delivery_date',
                                    'label' => 'Delivery Date'
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('admin.crud.fields.date', [
                                    'name' => 'date_from',
                                    'label' => 'Arrival Date'
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('admin.crud.fields.date', [
                                    'name' => 'date_to',
                                    'label' => 'Departure Date',
                                    'readonly' => true
                                ])
                            </div>

                            <div class="col-md-6">
                                @include('admin.crud.fields.textarea', [
                                    'name' => 'additional_note',
                                    'label' => 'Additional Note'
                                ])
                            </div>
                            <input type="hidden" name="booking_id" id="booking_id">
                        </div>

                        <button type="submit" class="btn btn-success mt-3">
                            Continue 
                        </button>
                    </div>

                </form>
            </div>
</div>

{{-- ================= JS ================= --}}
<script>
    let debounceTimer = null;
    let existingBookingData = null;
    let autoFilled = false;
    let searchMode = null;
    let nameFocusedOnce = false;

    const PACKAGE_DURATIONS = @json($packageDurations);

        function fillBookingSection(booking) {
        if (!booking) return;
        $('#booking_id').val(booking.id);
        $('#booking-section').removeClass('d-none');

        if (booking.cottage_package_id) {
            $('[name="cottage_package_id"]')
                .val(booking.cottage_package_id)
                .trigger('change.select2');
        }

        if (booking.is_delivered !== null) {
            $('[name="is_delivered"]')
                .val(booking.is_delivered)
                .trigger('change.select2');
        }

        $('[name="delivery_date"]').val(booking.delivery_date || '');
        $('[name="date_from"]').val(booking.date_from || '');
        $('[name="date_to"]').val(booking.date_to || '');
        $('[name="additional_note"]').val(booking.additional_note || '');
    }

    function checkClient() {
    
        const phone        = $('[name="phone"]').val();
        const email        = $('[name="email"]').val();
        const country_code = $('[name="country_code"]').val();
    
        // Guard
        if (
            (!phone && !email) ||
            (phone && !country_code && !email)
        ) {
            return;
        }
    
        fetch(`{{ route('admin.mandara-bookings.check-existing') }}?phone=${encodeURIComponent(phone)}&email=${encodeURIComponent(email)}&country_code=${encodeURIComponent(country_code)}`)
            .then(res => res.json())
            .then(res => {

            $('#client-message').addClass('d-none').text('');

            // ================= EXISTING CLIENT =================
            if (res.status === 1) {

                $('[name="name"]')
                    .val(res.data.name || '')
                    .prop('readonly', true);

                if (searchMode === 'phone' && res.data.email) {
                    $('[name="email"]').val(res.data.email);
                }

                if (searchMode === 'email' && res.data.phone) {
                    $('[name="phone"]').val(res.data.phone);
                }

                autoFilled = true;
                existingBookingData = res.data.booking || null;

                if (existingBookingData) {
                    fillBookingSection(existingBookingData);
                    $('#next-btn').addClass('d-none');
                } else {
                    $('#booking-section').addClass('d-none');
                    $('#next-btn').removeClass('d-none');
                }
            }

            // ================= NEW CLIENT =================
            else if (res.status === 0) {

                //  HARD RESET — THIS FIXES YOUR BUG
                autoFilled = false;
                existingBookingData = null;

                $('[name="name"]')
                    .val('')
                    .prop('readonly', false);

                $('#booking-section').addClass('d-none');
                $('#next-btn').removeClass('d-none');

                $('#client-message')
                    .removeClass('d-none')
                    .text('Client not found. Please enter details to create a new client.');
            }


            })
            .catch(err => {
            console.error('Client check failed', err);
            });
            }

    function calculateDepartureDate() {

        const packageId = $('[name="cottage_package_id"]').val();
        const arrival   = $('[name="date_from"]').val();

        if (!packageId || !arrival) return;

        const duration = parseInt(PACKAGE_DURATIONS[packageId]);

        if (!duration) return;

        let start = new Date(arrival);
        start.setDate(start.getDate() + duration - 1);

        const yyyy = start.getFullYear();
        const mm   = String(start.getMonth() + 1).padStart(2, '0');
        const dd   = String(start.getDate()).padStart(2, '0');

        $('[name="date_to"]').val(`${yyyy}-${mm}-${dd}`);
        }

        // Trigger on changes
        $(document).on('change', '[name="cottage_package_id"], [name="date_from"]', calculateDepartureDate);
    
    // Debounce inputs
       $(document).on('input', '[name="phone"]', function () {
            searchMode = 'phone';
            resetClientState('phone');
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(checkClient, 400);
        });

        $(document).on('input', '[name="email"]', function () {
            searchMode = 'email';
            resetClientState('email');
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(checkClient, 400);
        });

    
        $(document).on('change', '[name="country_code"]', function () {
            resetClientState('phone'); 
            checkClient();
        });
        document.getElementById('mandara-booking-form')
    .addEventListener('submit', function () {
        const btn = document.getElementById('submit-booking-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerText = 'Processing...';
        }
    });
    
    // Next button
        $('#next-btn').on('click', function () {
        $('#booking-section').removeClass('d-none');
        $(this).addClass('d-none');
    });
        
    function resetClientState() {

        autoFilled = false;
        existingBookingData = null;
        nameFocusedOnce = false;
        $('#booking_id').val('');
        $('#client-message').addClass('d-none').text('');

        $('[name="name"]').prop('readonly', false);

        //  CLEAR booking inputs explicitly
        $('#booking-section')
            .addClass('d-none')
            .find('input, textarea, select')
            .val('')
            .trigger('change');

        $('#next-btn').addClass('d-none');
        }

    </script>
    @if(isset($prefillBooking))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    
            $('#booking-section').removeClass('d-none');
            $('#next-btn').addClass('d-none');
    
            fillBookingSection(@json($prefillBooking));
        });
        @if(isset($prefillBooking))
        $('[name="phone"], [name="email"], [name="name"]').prop('readonly', true);
        @endif
    </script>
    @endif
@endsection
