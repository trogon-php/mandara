<?php

namespace App\Http\Requests\MandaraBookings;

use App\Http\Requests\BaseRequest;

class StoreMandaraBookingAdditionalDetailsRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $questions = $this->input('questions', []);

        foreach ($questions as &$q) {
            if (isset($q['answer']) && is_string($q['answer'])) {
                $q['answer'] = strtolower(trim($q['answer']));
            }
        }

        $this->merge([
            'questions' => $questions,
        ]);
    }
    public function rules(): array
    {
       
        return [
           
            // Additional details
            'blood_group'    => 'nullable|string|max:10',
            'is_veg'         => 'nullable|in:0,1',
            'diet_remarks'   => 'nullable|string',
            'address'        => 'nullable|string',
            'have_caretaker' => 'nullable|in:0,1',
            'have_siblings'  => 'nullable|in:0,1',
            'husband_name'   => 'nullable|string|max:255',

            // Medical questionnaire
            'questions' => 'nullable|array',
            'questions.*.answer' => 'sometimes|string|max:255',
            'questions.*.remarks' => 'nullable|string',

            // Images
            'images'   => 'nullable|array',
            'images.*' => 'nullable|file|image|max:2048',
            'consent' => 'required|accepted',

            'special_notes' => 'nullable|string',

            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'emergency_contact_country_code' => 'nullable|string|max:10',
            'emergency_contact_phone'        => 'nullable|string|max:20',
        ];
        
    }
}
