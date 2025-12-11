<?php
namespace App\Models\Traits;

trait AdminUser
{
    public function scopeAdmins($query)
    {
        return $query->where('role_id', 1);
    }

    public function canAccessAllCourses(): bool
    {
        return true;
    }
}
