
                @include('admin.crud.form', [
                    'action' => route('admin.mandara-bookings.update', $booking->id),
                    'formId' => 'edit-mandara-booking-form',
                    'submitText' => 'Update Booking Details',
                    'class'      => 'ajax-crud-form',
                    'redirect'   => route('admin.mandara-bookings.index'),
                    'method' => 'PUT',
                    'fields' => [

                        [
                            'type' => 'text',
                            'name' => 'blood_group',
                            'label' => 'Blood Group',
                            'value' => $booking->blood_group,
                            'col' => 6
                        ],

                        [
                            'type' => 'select',
                            'name' => 'is_veg',
                            'label' => 'Vegetarian',
                            'options' => [
                                '1' => 'Yes',
                                '0' => 'No',
                            ],
                            'value' => (string)$booking->is_veg,
                            'col' => 6
                        ],

                        [
                            'type' => 'textarea',
                            'name' => 'diet_remarks',
                            'label' => 'Diet Remarks',
                            'value' => $booking->diet_remarks,
                            'col' => 6
                        ],

                        [
                            'type' => 'textarea',
                            'name' => 'address',
                            'label' => 'Address',
                            'value' => $booking->address,
                            'col' => 6
                        ],

                        [
                            'type' => 'select',
                            'name' => 'have_caretaker',
                            'label' => 'Have Caretaker',
                            'options' => [
                                '1' => 'Yes',
                                '0' => 'No',
                            ],
                            'value' => (string)$booking->have_caretaker,
                            'col' => 6
                        ],
                        [
                            'type' => 'text',
                            'name' => 'caretaker_name',
                            'label' => 'Caretaker Name',
                            'value' => $booking->caretaker_name,
                            'col' => 6
                        ],
                        [
                            'type' => 'text',
                            'name' => 'caretaker_age',
                            'label' => 'Caretaker Age',
                            'value' => $booking->caretaker_age,
                            'col' => 6
                        ],

                        [
                            'type' => 'select',
                            'name' => 'have_siblings',
                            'label' => 'Have Siblings',
                            'options' => [
                                '1' => 'Yes',
                                '0' => 'No',
                            ],
                            'value' => (string)$booking->have_siblings,
                            'col' => 6
                        ],
                        [
                            'type' => 'text',
                            'name' => 'sibling_name',
                            'label' => 'Sibling Name',
                            'value' => $booking->sibling_name,
                            'col' => 6
                        ],

                        [
                            'type' => 'text',
                            'name' => 'husband_name',
                            'label' => 'Spouse Name',
                            'value' => $booking->husband_name,
                            'col' => 6
                        ],

                        [
                            'type' => 'textarea',
                            'name' => 'additional_note',
                            'label' => 'Additional Note',
                            'value' => $booking->additional_note,
                            'col' => 6
                        ],
                        [
                            'type'=>'files',
                            'name'=>'images',
                            'label'=>'Images',
                            'presetKey' => 'mandara_bookings_image',
                            'multiple' => true,
                            'accept' => 'image/*',
                            'value' => $booking->images_url,
                            'col'=>12
                        ],

                    ]
                ])


