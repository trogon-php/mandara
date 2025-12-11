<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Base\RoleBaseController;

abstract class TutorBaseController extends RoleBaseController
{
    protected string $guard = 'tutor';
    protected ?int $requiredRoleId = 3;

    protected function redirectToDashboard()
    {
        return redirect()->route('tutor.dashboard');
    }
}
