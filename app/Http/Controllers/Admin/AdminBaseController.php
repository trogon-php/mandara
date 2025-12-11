<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\RoleBaseController;
use App\Http\Controllers\Admin\Traits\AdminControllerHelpers;

abstract class AdminBaseController extends RoleBaseController
{
    protected string $guard = 'admin';
    protected ?int $requiredRoleId = 1;

    use AdminControllerHelpers;

    protected function redirectToDashboard()
    {
        return redirect()->route('admin.dashboard');
    }
}
