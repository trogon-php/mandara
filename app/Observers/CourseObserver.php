<?php

namespace App\Observers;

class CourseObserver extends BaseObserver
{
    protected function getCachePrefixes($model, string $event): array
    {
        $prefixes = [
            'courses',       // CourseService cache
            'my_course',     // MyCourseService cache (course details)
            'dashboard',     // Dashboard cache
        ];

        // If course category changed, also clear categories cache
        if ($event === 'updated' && $model->wasChanged('category_id')) {
            $prefixes[] = 'categories';
        }

        return array_unique($prefixes);
    }
}
