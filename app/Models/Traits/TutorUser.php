<?php
namespace App\Models\Traits;

trait TutorUser
{
    public function scopeTutors($query)
    {
        return $query->where('role_id', 3);
    }

    public function assignedCourses()
    {
        return $this->hasMany(Course::class, 'user_id');
    }
}
