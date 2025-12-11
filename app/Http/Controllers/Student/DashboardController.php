<?php

namespace App\Http\Controllers\Student;

class DashboardController extends StudentBaseController
{
    public function index()
    {
        $user = $this->user();
        return view('student.dashboard', compact('user'));
    }
}
