@extends('admin.layouts.app')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $page_title ?? 'Additional Details' }}</h4>

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
                    <i class="mdi mdi-arrow-left"></i>
                    Back to Mandara Bookings
                </a>
            </div>

            <div class="card-body">

                <form
                    action="{{ route('admin.mandara-bookings.additional-details-store', $booking->id) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="add-mandara-booking-form"
                >
                    @csrf

                    {{-- ================= BASIC DETAILS ================= --}}
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Blood Group</label>
                            <input type="text"
                                   name="blood_group"
                                   class="form-control"
                                   value="{{ old('blood_group', $booking->blood_group) }}"
                                   placeholder="Enter the blood group">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vegetarian Status</label>
                            <select name="is_veg" class="form-control">
                                <option value="">Select</option>
                        
                                <option value="1"
                                    {{ old('is_veg', $booking->is_veg) == 1 ? 'selected' : '' }}>
                                    Yes
                                </option>
                        
                                <option value="0"
                                    {{ old('is_veg', $booking->is_veg) == 0 ? 'selected' : '' }}>
                                    No
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diet Remarks</label>
                                      <textarea name="diet_remarks"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Enter the diet remarks">{{ old('diet_remarks', $booking->diet_remarks) }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" 
                                      class="form-control"
                                      rows="2"
                                      placeholder="Enter the address">{{ old('address', $booking->address) }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Have Caretaker</label>
                            <select name="have_caretaker" class="form-control" value="{{ old('have_caretaker', $booking->have_caretaker) }}">
                                <option value="">Select</option>
                                <option value="1" {{ old('have_caretaker', $booking->have_caretaker) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('have_caretaker', $booking->have_caretaker) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Have Siblings</label>
                            <select name="have_siblings" class="form-control" value="{{ old('have_siblings', $booking->have_siblings) }}">
                                <option value="">Select</option>
                                <option value="1" {{ old('have_siblings', $booking->have_siblings) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('have_siblings', $booking->have_siblings) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Spouse Name</label>
                            <input type="text" value="{{ old('husband_name', $booking->husband_name) }}"
                                   name="husband_name"
                                   class="form-control"
                                   placeholder="Enter the spouse name">
                        </div>

                        @if(!empty($booking->images))
                        <div class="row mt-2">
                            @foreach($booking->images as $image)
                                <div class="col-md-3 mb-2 text-center">
                                    <img src="{{ asset($image) }}"
                                        class="img-thumbnail"
                                        style="height:120px; object-fit:cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                   {{-- ================= ADDITIONAL NOTES & EMERGENCY CONTACT ================= --}}
                    <hr>

                    <h5 class="mb-3">Additional Notes & Emergency Contact</h5>

                    <div class="row">

                        {{-- Special Notes --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Special Notes</label>
                            <textarea
                                name="special_notes"
                                class="form-control"
                                rows="3"
                                placeholder="Any special instructions, medical notes, or important information"
                            >{{ old('special_notes', $booking->special_notes) }}</textarea>
                        </div>

                        {{-- Emergency Contact Name --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Emergency Contact Name</label>
                            <input
                                type="text"
                                name="emergency_contact_name"
                                class="form-control"
                                placeholder="Enter contact name"
                                value="{{ old('emergency_contact_name', $booking->emergency_contact_name) }}"
                            >
                        </div>

                        {{-- Emergency Contact Relationship --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Relationship</label>
                            <input
                                type="text"
                                name="emergency_contact_relationship"
                                class="form-control"
                                placeholder="e.g. Husband, Parent, Sibling"
                                value="{{ old('emergency_contact_relationship', $booking->emergency_contact_relationship) }}"
                            >
                        </div>

                        {{-- Emergency Contact Phone --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Emergency Contact Phone</label>

                            <div class="row g-2">
                                <div class="col-5">
                                    @include('admin.crud.fields.country-code', [
                                        'name'  => 'emergency_contact_country_code',
                                        'label' => null,
                                        'value' => $countryCode ?? '91'
                                    ])
                                </div>

                                <div class="col-7">
                                    <input
                                        type="text"
                                        name="emergency_contact_phone"
                                        class="form-control"
                                        placeholder="Phone number"
                                        value="{{ old('emergency_contact_phone', $phoneNumber ?? '') }}">
                                        
                                </div>
                            </div>
                        </div>
                        

                    </div>


                    </div>

                    <hr>

                    {{-- ================= MEDICAL QUESTIONNAIRE ================= --}}
                    <h5 class="mb-3">Mandara Guest Pre Arrival Onboarding Questionnaire</h5>

                    <h3 class="mt-4">SECTION A – CORE DETAILS (For All Guests)</h3>

                    {{-- Arrival (STATIC) --}}
                    <h5 class="mt-3">Arrival</h5>
                    <p>
                        Arrival date at Mandara:
                        <strong>{{ $booking->date_from }}</strong>
                    </p>

                    {{-- Mother’s Health – General (STATIC HEADING) --}}
                    <h5 class="mt-4">Mother’s Health – General</h5>

                    @php
                        // Question IDs that belong to this heading
                        $motherHealthQuestionIds = [61, 62, 63, 64, 65]; // adjust as needed
                    @endphp

                    @foreach($questions as $question)
                        @continue(!in_array($question->id, $motherHealthQuestionIds))

                        @php
                            /*
                            |--------------------------------------------------------------------------
                            | Normalize saved answer
                            |--------------------------------------------------------------------------
                            */
                            $rawSavedAnswer = old(
                                "questions.$question->id.answer",
                                $answers[$question->id]->answer ?? null
                            );

                            if (is_string($rawSavedAnswer)) {
                                $savedAnswers = [$rawSavedAnswer];
                            } elseif (is_array($rawSavedAnswer)) {
                                $savedAnswers = $rawSavedAnswer;
                            } else {
                                $savedAnswers = [];
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Normalize OPTIONS (DB = option_text)
                            |--------------------------------------------------------------------------
                            */
                            $options = [];

                            if (is_array($question->options)) {
                                $options = collect($question->options)->map(function ($opt) {
                                    return [
                                        'value' => $opt['option_text'],
                                        'label' => $opt['option_text'],
                                    ];
                                })->all();
                            }
                        @endphp


                        <div class="card mb-3">
                            <div class="card-body">

                                <p class="fw-bold mb-2">{{ $question->question }}</p>

                                {{-- RADIO --}}
                                @if(count($options))
                                <div class="mb-2">
                                    @foreach($question->normalized_options as $option)
                                        <label class="me-3">
                                            <input
                                                type="radio"
                                                name="questions[{{ $question->id }}][answer]"
                                                value="{{ $option['value'] }}"
                                                {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                            >
                                            {{ $option['label'] }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                                {{-- REMARK --}}
                                @if($question->require_remark)
                                    <textarea
                                        name="questions[{{ $question->id }}][remarks]"
                                        class="form-control mt-2"
                                        rows="2"
                                    >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                                @endif

                            </div>
                        </div>
                    @endforeach

                    @php
                    $pregnancyDeliveryStatusQuestionIds = [67];
                    @endphp

                    <h5 class="mt-4">Pregnancy & Delivery Status</h5>

                    @foreach($questions as $question)
                        @continue(!in_array($question->id, $pregnancyDeliveryStatusQuestionIds))

                        @php
                            /*
                            |--------------------------------------------------------------------------
                            | Normalize saved answer
                            |--------------------------------------------------------------------------
                            */
                            $rawSavedAnswer = old(
                                "questions.$question->id.answer",
                                $answers[$question->id]->answer ?? null
                            );

                            if (is_string($rawSavedAnswer)) {
                                $savedAnswers = [$rawSavedAnswer];
                            } elseif (is_array($rawSavedAnswer)) {
                                $savedAnswers = $rawSavedAnswer;
                            } else {
                                $savedAnswers = [];
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Normalize OPTIONS (DB = option_text)
                            |--------------------------------------------------------------------------
                            */
                            $options = [];

                            if (is_array($question->options)) {
                                $options = collect($question->options)->map(function ($opt) {
                                    return [
                                        'value' => $opt['option_text'],
                                        'label' => $opt['option_text'],
                                    ];
                                })->all();
                            }
                        @endphp


                        <div class="card mb-3">
                            <div class="card-body">

                                <p class="fw-bold mb-2">{{ $question->question }}</p>

                                {{-- RADIO --}}
                                @if(count($options))
                                    <div class="mb-2">
                                        @foreach($question->normalized_options as $option)
                                            <label class="me-3">
                                                <input
                                                    type="radio"
                                                     class="delivery-status-radio"
                                                    name="questions[{{ $question->id }}][answer]"
                                                    value="{{ $option['value'] }}"
                                                    {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                                >
                                                {{ $option['label'] }}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- REMARK --}}
                                @if($question->require_remark)
                                    <textarea
                                        name="questions[{{ $question->id }}][remarks]"
                                        class="form-control mt-2"
                                        rows="2"
                                    >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                                @endif

                            </div>
                        </div>
                    @endforeach
                <div id="section-b">
                    <h3 class="mt-4">SECTION B – PRE DELIVERY QUESTIONS (For Pregnant Guests)</h3>
                    <h5 class="mt-4">Pregnancy Details</h5>
                    Expected Delivery Date:
                    <strong>{{ $booking->delivery_date }}</strong>
                    Current Gestational Age:
                    <strong>5 weeks</strong>

                    <h5 class="mt-4">Obstetrical Background</h5>
                    @php
                    $ObstetricBackgroundIds = [42,43,44];
                    @endphp

                        @foreach($questions as $question)
                        @continue(!in_array($question->id, $ObstetricBackgroundIds))

                        @php
                            /*
                            |--------------------------------------------------------------------------
                            | Normalize saved answer
                            |--------------------------------------------------------------------------
                            */
                            $rawSavedAnswer = old(
                                "questions.$question->id.answer",
                                $answers[$question->id]->answer ?? null
                            );

                            if (is_string($rawSavedAnswer)) {
                                $savedAnswers = [$rawSavedAnswer];
                            } elseif (is_array($rawSavedAnswer)) {
                                $savedAnswers = $rawSavedAnswer;
                            } else {
                                $savedAnswers = [];
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Normalize OPTIONS (DB = option_text)
                            |--------------------------------------------------------------------------
                            */
                            $options = [];

                            if (is_array($question->options)) {
                                $options = collect($question->options)->map(function ($opt) {
                                    return [
                                        'value' => $opt['option_text'],
                                        'label' => $opt['option_text'],
                                    ];
                                })->all();
                            }
                        @endphp


                        <div class="card mb-3">
                            <div class="card-body">

                                <p class="fw-bold mb-2">{{ $question->question }}</p>

                                {{-- RADIO --}}
                                @if(count($options))
                                    <div class="mb-2">
                                        @foreach($question->normalized_options as $option)
                                            <label class="me-3">
                                                <input
                                                    type="radio"
                                                    name="questions[{{ $question->id }}][answer]"
                                                    value="{{ $option['value'] }}"
                                                    {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                                >
                                                {{ $option['label'] }}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- REMARK --}}
                                @if($question->require_remark)
                                    <textarea
                                        name="questions[{{ $question->id }}][remarks]"
                                        class="form-control mt-2"
                                        rows="2"
                                    >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                                @endif

                            </div>
                        </div>
                    @endforeach

                    <h5 class="mt-4">Pregnancy Related Medical History</h5>

                    @php
                    $PregnancyRelatedMedicalHistoryIds = [45,46];
                    @endphp
                    @foreach($questions as $question)
                    @continue(!in_array($question->id, $PregnancyRelatedMedicalHistoryIds))

                    @php
                        /*
                        |--------------------------------------------------------------------------
                        | Normalize saved answer
                        |--------------------------------------------------------------------------
                        */
                        $rawSavedAnswer = old(
                            "questions.$question->id.answer",
                            $answers[$question->id]->answer ?? null
                        );

                        if (is_string($rawSavedAnswer)) {
                            $savedAnswers = [$rawSavedAnswer];
                        } elseif (is_array($rawSavedAnswer)) {
                            $savedAnswers = $rawSavedAnswer;
                        } else {
                            $savedAnswers = [];
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Normalize OPTIONS (DB = option_text)
                        |--------------------------------------------------------------------------
                        */
                        $options = [];

                        if (is_array($question->options)) {
                            $options = collect($question->options)->map(function ($opt) {
                                return [
                                    'value' => $opt['option_text'],
                                    'label' => $opt['option_text'],
                                ];
                            })->all();
                        }
                    @endphp


                    <div class="card mb-3">
                        <div class="card-body">

                            <p class="fw-bold mb-2">{{ $question->question }}</p>

                            {{-- RADIO --}}
                            @if(count($options))
                                <div class="mb-2">
                                    @foreach($question->normalized_options as $option)
                                        <label class="me-3">
                                            <input
                                                type="radio"
                                                name="questions[{{ $question->id }}][answer]"
                                                value="{{ $option['value'] }}"
                                                {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                            >
                                            {{ $option['label'] }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            {{-- REMARK --}}
                            @if($question->require_remark)
                                <textarea
                                    name="questions[{{ $question->id }}][remarks]"
                                    class="form-control mt-2"
                                    rows="2"
                                >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                            @endif

                        </div>
                    </div>
                @endforeach



                <h5 class="mt-4">Current Pregnancy Concerns</h5>
                @php
                $currentPregnancyConcernsIds = [48,49,50];
                @endphp
                @foreach($questions as $question)
                @continue(!in_array($question->id, $currentPregnancyConcernsIds))

                @php
                    /*
                    |--------------------------------------------------------------------------
                    | Normalize saved answer
                    |--------------------------------------------------------------------------
                    */
                    $rawSavedAnswer = old(
                        "questions.$question->id.answer",
                        $answers[$question->id]->answer ?? null
                    );

                    if (is_string($rawSavedAnswer)) {
                        $savedAnswers = [$rawSavedAnswer];
                    } elseif (is_array($rawSavedAnswer)) {
                        $savedAnswers = $rawSavedAnswer;
                    } else {
                        $savedAnswers = [];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Normalize OPTIONS (DB = option_text)
                    |--------------------------------------------------------------------------
                    */
                    $options = [];

                    if (is_array($question->options)) {
                        $options = collect($question->options)->map(function ($opt) {
                            return [
                                'value' => $opt['option_text'],
                                'label' => $opt['option_text'],
                            ];
                        })->all();
                    }
                @endphp


                <div class="card mb-3">
                    <div class="card-body">

                        <p class="fw-bold mb-2">{{ $question->question }}</p>

                        {{-- RADIO --}}
                        @if(count($options))
                            <div class="mb-2">
                                @foreach($question->normalized_options as $option)
                                    <label class="me-3">
                                        <input
                                            type="radio"
                                            name="questions[{{ $question->id }}][answer]"
                                            value="{{ $option['value'] }}"
                                            {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                        >
                                        {{ $option['label'] }}
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- REMARK --}}
                        @if($question->require_remark)
                            <textarea
                                name="questions[{{ $question->id }}][remarks]"
                                class="form-control mt-2"
                                rows="2"
                            >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                        @endif

                    </div>
                </div>
            @endforeach


            <h5 class="mt-4">Digestive, Urinary & Bowel Health</h5>
            @php
            $digestiveUrinaryBowelHealthIds = [51,52,53];
            @endphp
            @foreach($questions as $question)
            @continue(!in_array($question->id, $digestiveUrinaryBowelHealthIds))

            @php
                /*
                |--------------------------------------------------------------------------
                | Normalize saved answer
                |--------------------------------------------------------------------------
                */
                $rawSavedAnswer = old(
                    "questions.$question->id.answer",
                    $answers[$question->id]->answer ?? null
                );

                if (is_string($rawSavedAnswer)) {
                    $savedAnswers = [$rawSavedAnswer];
                } elseif (is_array($rawSavedAnswer)) {
                    $savedAnswers = $rawSavedAnswer;
                } else {
                    $savedAnswers = [];
                }

                /*
                |--------------------------------------------------------------------------
                | Normalize OPTIONS (DB = option_text)
                |--------------------------------------------------------------------------
                */
                $options = [];

                if (is_array($question->options)) {
                    $options = collect($question->options)->map(function ($opt) {
                        return [
                            'value' => $opt['option_text'],
                            'label' => $opt['option_text'],
                        ];
                    })->all();
                }
            @endphp


            <div class="card mb-3">
                <div class="card-body">

                    <p class="fw-bold mb-2">{{ $question->question }}</p>

                    {{-- RADIO --}}
                    @if(count($options))
                        <div class="mb-2">
                            @foreach($question->normalized_options as $option)
                                <label class="me-3">
                                    <input
                                        type="radio"
                                        name="questions[{{ $question->id }}][answer]"
                                        value="{{ $option['value'] }}"
                                        {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                    >
                                    {{ $option['label'] }}
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- REMARK --}}
                    @if($question->require_remark)
                        <textarea
                            name="questions[{{ $question->id }}][remarks]"
                            class="form-control mt-2"
                            rows="2"
                        >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                    @endif

                </div>
            </div>
        @endforeach

           <h5 class="mt-4">Emotional Well-being</h5>

           @php
           $emotionalWellbeingIds = [54,70];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $emotionalWellbeingIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach


       <h5 class="mt-4">Food Preferences</h5>
       @php
           $foodPreferencesIds = [55,71];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $foodPreferencesIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
</div>
<div id="section-c">
       <h3 class="mt-4">SECTION C – POST-DELIVERY QUESTIONS</h3>
       <h5 class="mt-4">Delivery Details</h5>
       <p>
       Date of delivery:
       <strong>{{ $booking->delivery_date }}</strong>
       @php
           $deliveryDetailsIds = [56];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $deliveryDetailsIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Obstetric History</h5>
       @php
           $obstetricHistoryIds = [42,43,44];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $obstetricHistoryIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Post-Delivery Recovery</h5>
       @php
           $postDeliveryRecoveryIds = [72,73,74];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $postDeliveryRecoveryIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Current Physical Symptoms</h5>
       @php
           $currentPhysicalSymptomsIds = [75,76,77];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $currentPhysicalSymptomsIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Digestive, Urinary & Bowel Health</h5>
       @php
           $digestiveUrinaryBowelHealthIds = [51,52,53];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $digestiveUrinaryBowelHealthIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Baby Details</h5>
       @php
           $babyDetailsIds = [57,79,80];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $babyDetailsIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Feeding & Breast Health</h5>
       @php
           $feedingBreastHealthIds = [81,82,83];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $feedingBreastHealthIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Emotional Well-being After Delivery</h5>
       @php
           $emotionalWellbeingAfterDeliveryIds = [84];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $emotionalWellbeingAfterDeliveryIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach
       <h5 class="mt-4">Food Preferences & Wellness Goal</h5>
       @php
           $foodPreferencesIds = [55,71];
           @endphp
           @foreach($questions as $question)
           @continue(!in_array($question->id, $foodPreferencesIds))

           @php
               /*
               |--------------------------------------------------------------------------
               | Normalize saved answer
               |--------------------------------------------------------------------------
               */
               $rawSavedAnswer = old(
                   "questions.$question->id.answer",
                   $answers[$question->id]->answer ?? null
               );

               if (is_string($rawSavedAnswer)) {
                   $savedAnswers = [$rawSavedAnswer];
               } elseif (is_array($rawSavedAnswer)) {
                   $savedAnswers = $rawSavedAnswer;
               } else {
                   $savedAnswers = [];
               }

               /*
               |--------------------------------------------------------------------------
               | Normalize OPTIONS (DB = option_text)
               |--------------------------------------------------------------------------
               */
               $options = [];

               if (is_array($question->options)) {
                   $options = collect($question->options)->map(function ($opt) {
                       return [
                           'value' => $opt['option_text'],
                           'label' => $opt['option_text'],
                       ];
                   })->all();
               }
           @endphp


           <div class="card mb-3">
               <div class="card-body">

                   <p class="fw-bold mb-2">{{ $question->question }}</p>

                   {{-- RADIO --}}
                   @if(count($options))
                       <div class="mb-2">
                           @foreach($question->normalized_options as $option)
                               <label class="me-3">
                                   <input
                                       type="radio"
                                       name="questions[{{ $question->id }}][answer]"
                                       value="{{ $option['value'] }}"
                                       {{ in_array((string)$option['value'], array_map('strval', $savedAnswers), true) ? 'checked' : '' }}
                                   >
                                   {{ $option['label'] }}
                               </label>
                           @endforeach
                       </div>
                   @endif

                   {{-- REMARK --}}
                   @if($question->require_remark)
                       <textarea
                           name="questions[{{ $question->id }}][remarks]"
                           class="form-control mt-2"
                           rows="2"
                       >{{ old("questions.$question->id.remarks", $answers[$question->id]->remarks ?? '') }}</textarea>
                   @endif

               </div>
           </div>
       @endforeach


 </div>

                    
                    {{-- ================= CONSENT ================= --}}
                  
                    <div class="form-check mt-3">
                        {{-- hidden field for unchecked state --}}
                        <input type="hidden" name="consent" value="0">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="consent_checkbox"
                            name="consent"
                            value="1"
                            {{ (old('consent') !== null
                                ? old('consent') == 1
                                : ($booking->consent ?? 0) == 1) ? 'checked' : '' }}>
                        

                        <label class="form-check-label" for="consent_checkbox">
                            I have read and agree to the
                            <a href="javascript:void(0)" onclick="openConsentModal()">
                                Terms & Conditions
                            </a>
                        </label>
                    </div>



                    {{-- ================= SUBMIT ================= --}}
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            Save Additional Details
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<!-- Consent Modal -->
<div class="modal fade" id="consentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Terms & Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- YOUR TERMS CONTENT --}}
                Terms, Conditions & Consent
By proceeding with the booking, onboarding, and entry into Mandara, I confirm that I have read, understood, and agree to the following terms and conditions.
• I confirm that all information provided by me during booking and onboarding is true, accurate, and complete to the best of my knowledge.
• I understand that my participation at Mandara is voluntary and based on my personal choice.
• I acknowledge that my wellness programme and services will be planned based solely on the information provided by me and my disclosed health status.
• I agree to immediately inform the Mandara team of any change in my health condition, pregnancy status, delivery status, medications, or overall well-being during my stay.
• I understand that Mandara shall not be responsible for any outcomes arising from incomplete, incorrect, delayed, or undisclosed information provided by me.
• I clearly understand and agree that Mandara offers wellness-based, supportive, and recuperative services only.
• I acknowledge that Mandara does not provide medical diagnosis, medical treatment, surgical care, or emergency medical services and does not function as a hospital or medical facility.
• I understand that Mandara’s services do not replace or substitute professional medical care, obstetric care, paediatric care, psychiatric care, or emergency treatment.
• I confirm that I am medically stable to participate in wellness services and that I will seek independent medical care whenever required.
• I understand that all wellness therapies, care protocols, and treatment guidelines at Mandara will be solely decided by the doctor in charge of the wellness segment.
• I acknowledge that wellness programmes and therapy plans will be structured, modified, postponed, or restricted based on my health status, safety considerations, and professional assessment.
• I understand that the wellness therapies offered are commonly structured and that any modification will be done only after assessment and with approval from the Mandara team or the consulting doctor.
• I understand that Mandara reserves the right to modify, restrict, or discontinue any service if my condition is found unsuitable or if safety, ethical, or legal concerns arise.
• I acknowledge that I may decline or discontinue any therapy at any point after informing the care team.
• I understand that in the event Mandara identifies a situation requiring medical attention, I will be advised to seek external medical care.
• I agree that Mandara is not responsible for delays, outcomes, or consequences arising from external medical consultations, referrals, hospital admissions, or emergency services.
• I acknowledge that all costs related to medical consultations, investigations, hospitalisation, transport, or emergency care shall be borne solely by me.
• I agree to follow Mandara’s centre rules, safety guidelines, and code of conduct at all times.
• I understand that the comfort, privacy, dignity, and safety of other guests and staff must not be disturbed.
• I agree to comply with Mandara’s strict substance-free premises policy, including no alcohol, smoking, vaping, or substance use.
• I confirm that I have truthfully disclosed my alcohol, smoking, or substance-use history and understand that this may affect therapy suitability.
• I understand that any misconduct, misbehaviour, verbal or physical abuse, inappropriate conduct, or non-compliance may result in immediate restriction or termination of services without refund.
• I understand that the presence of any accompanying person is subject to Mandara’s policies.
• I acknowledge that the safety, supervision, and well-being of any accompanying child or children are solely my responsibility and not Mandara’s responsibility at any point.
• I agree that Mandara shall not be held responsible for any injury, illness, loss, or incident involving accompanying persons or accompanying children.
• I understand that visitors are not permitted inside guest rooms under any circumstances.
• I acknowledge that visitors, if allowed, may access only designated common areas such as the restaurant or dining area, in order to protect the privacy, comfort, and safety of other guests.
• I understand that any additional amenities, services, or requests outside the confirmed package will be chargeable and must be paid for separately.
• I acknowledge that food, refreshments, or services requested for visiting guests or additional persons will be charged additionally as per Mandara’s applicable rates.
• I understand that Mandara is not responsible for loss, theft, or damage to my personal belongings.
• I acknowledge that all property, infrastructure, equipment, furnishings, and assets within the Mandara premises belong to Mandara.
• I agree to take reasonable care of all Mandara property and understand that any damage, misuse, or loss caused by me or my accompanying persons may be charged to me as assessed by management.
• I confirm that I have no pending legal, police, or statutory cases that affect my participation at Mandara.
• I agree that Mandara, its promoters, management, staff, consultants, and service partners shall not be held liable for any injury, illness, loss, emotional distress, or adverse outcome arising during or after my stay, except where required by law.
• I agree to fully indemnify and hold harmless Mandara from any claims, damages, penalties, legal costs, or liabilities arising from my actions, omissions, non-disclosure, or non-compliance.
• I acknowledge that Mandara shall not be liable for interruptions, modifications, or cancellations of services due to force majeure events including natural calamities, government orders, public health emergencies, or technical failures.
• I consent to the confidential internal use of my information for assessment, care planning, safety, operational, and quality improvement purposes.
• I understand that my information will not be disclosed externally except where required by law.
• I confirm that I have read and understood all the above terms and conditions.
• I voluntarily agree to participate in Mandara’s wellness programme under these terms.
• I acknowledge that this consent is binding throughout my stay at Mandara.

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="acceptConsent()"
                >
                    I Agree
                </button>
            </div>

        </div>
    </div>
</div>
<script>
    function openConsentModal() {
        const modal = new bootstrap.Modal(document.getElementById('consentModal'));
        modal.show();
    }

    function acceptConsent() {
        document.getElementById('consent_checkbox').checked = true;
        bootstrap.Modal.getInstance(
            document.getElementById('consentModal')
        ).hide();
    }

    document.getElementById('add-mandara-booking-form')
        .addEventListener('submit', function (e) {

            const consentChecked =
                document.getElementById('consent_checkbox').checked;

            if (!consentChecked) {
                e.preventDefault();
                openConsentModal();
            }
        });
        document.addEventListener('DOMContentLoaded', function () {

        const sectionB = document.getElementById('section-b');
        const sectionC = document.getElementById('section-c');
        const radios   = document.querySelectorAll('.delivery-status-radio');
        console.log(radios);

        function toggleSections(value) {
            value = value.toLowerCase();

            if (value.includes('before')) {
                sectionB.style.display = 'block';
                sectionC.style.display = 'none';
            } 
            else if (value.includes('after')) {
                sectionB.style.display = 'none';
                sectionC.style.display = 'block';
            }
            else {
                sectionB.style.display = 'none';
                sectionC.style.display = 'none';
            }
        }

        // Initial load (edit / old input)
        radios.forEach(radio => {
            if (radio.checked) {
                toggleSections(radio.value);
            }
        });

        //  On change
        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                toggleSections(this.value);
            });
        });
        });
</script> 
@endsection
