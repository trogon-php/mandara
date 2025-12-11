<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Base\RoleBaseController;

abstract class StudentBaseController extends RoleBaseController
{
    protected string $guard = 'student';
    protected ?int $requiredRoleId = 2;

    protected function redirectToDashboard()
    {
        return redirect()->route('student.dashboard');
    }
}
