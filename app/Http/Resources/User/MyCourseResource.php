<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class MyCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'shortcuts' => config('client.app.my_course.shortcuts'),
            'course_units' => $this->course->rootCourseUnits->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'title' => $unit->title,
                    'thumbnail' => is_array($unit->thumbnail) ? $unit->thumbnail_url['thumb'] : null,
                    'is_locked' => $unit->access_type == 'free' ? false : true,
                    'lock_type' => $unit->access_type == 'free' ? null : 'purchase',
                    'lock_message' => $unit->access_type == 'free' ? null : 'This unit is locked. Please purchase it to access it.',
                    'is_completed' => false,
                    'progress' => 0,
                    // Use optimized methods that leverage eager loaded relationships
                    'lessons_count' => $unit->getLessonsCountAttribute(),
                    'materials_count' => $unit->getMaterialsCountByType(),
                ];
            }),
            'upcoming_liveclasses' => [], // Empty array as requested
            'promotion' => config('client.app.my_course.promotion'),
            'exam_url' => config('client.app.my_course.exam_url'),
        ];
    }
}
