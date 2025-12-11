<?php
namespace App\Models\Traits;

use App\Models\Course;

trait StudentUser
{
    public function scopeStudents($query)
    {
        return $query->where('role_id', 3);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }
}
