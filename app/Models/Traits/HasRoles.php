<?php

namespace App\Models\Traits;

use App\Enums\Role;

trait HasRoles
{
    // Query scopes
    public function scopeStudents($query)
    {
        return $query->where('role_id', Role::STUDENT->value);
    }

    public function scopeTutors($query)
    {
        return $query->where('role_id', Role::TUTOR->value);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role_id', Role::ADMIN->value);
    }

    // Convenience checks
    public function isStudent(): bool
    {
        return $this->role_id === Role::STUDENT->value;
    }

    public function isTutor(): bool
    {
        return $this->role_id === Role::TUTOR->value;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === Role::ADMIN->value;
    }
}
