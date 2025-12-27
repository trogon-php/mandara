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
                    'action' => route('admin.mandara-bookings.additional-details-store', $booking->id),
                    'formId' => 'add-mandara-booking-form',
                    'submitText' => 'Save Additional Details',
                    'fields' => [
                       
                       
                        [
                            'type' => 'text',
                            'name' => 'blood_group',
                            'id' => 'blood_group',
                            'label' => 'Blood Group',
                            'placeholder' => 'Enter the blood group',
                            'required' => false,
                            'col' => 12
                        ],
                        [
                            'type' => 'select',
                            'name' => 'is_veg',
                            'id' => 'is_veg',
                            'label' => 'Vegetarian Status',
                            'placeholder' => 'Select the vegetarian status',
                            'options' => [
                                '1' => 'Yes',
                                '0' => 'No',
                            ],
                            'required' => false,
                            'col' => 12
                        ], 
                        [
                            'type' => 'textarea',
                            'name' => 'diet_remarks',
                            'id' => 'diet_remarks',
                            'label' => 'Diet Remarks',
                            'placeholder' => 'Enter the diet remarks',
                            'required' => false,
                            'col' => 12
                        ],
                        [
                            'type' => 'text',
                            'name' => 'address',
                            'id' => 'address',
                            'label' => 'Address',
                            'placeholder' => 'Enter the address',
                            'required' => false,
                            'col' => 12
                        ],
                        [
                            'type' => 'select',
                            'name' => 'have_caretaker',
                            'id' => 'have_caretaker',
                            'label' => 'Have Caretaker',
                            'placeholder' => 'Select the caretaker status',
                            'options' => [
                                '1' => 'Yes',
                                '0' => 'No',
                            ],
                            'required' => false,
                            'col' => 12
                        ],
                        [
                            'type' => 'select',
                            'name' => 'have_siblings',
                            'id' => 'have_siblings',
                            'label' => 'Have Siblings',
                            'placeholder' => 'Select the siblings status',
                            'options' => [
                                '1' => 'Yes',
                                '0' => 'No',
                            ],
                            'required' => false,
                            'col' => 12
                        ],

                        [
                            'type' => 'text',
                            'name' => 'husband_name',
                            'id' => 'husband_name',
                            'label' => 'Husband Name',
                            'placeholder' => 'Enter the husband name',
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
