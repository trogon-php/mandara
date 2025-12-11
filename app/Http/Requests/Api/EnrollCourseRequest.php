<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Validation\Rule;

class EnrollCourseRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'course_id' => 'required|integer|exists:courses,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->course_id) {
                $userId = auth()->id();
                $exists = \App\Models\Enrollment::where('user_id', $userId)
                    ->where('course_id', $this->course_id)
                    ->exists();
                
                if ($exists) {
                    $validator->errors()->add('course_id', 'Already enrolled in this course.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'Course is required.',
            'course_id.exists' => 'Selected course does not exist.',
        ];
    }
}
