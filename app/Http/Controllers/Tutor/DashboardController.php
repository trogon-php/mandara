<?php

namespace App\Http\Controllers\Tutor;

class DashboardController extends TutorBaseController
{
    public function index()
    {
        $user = $this->user();
        return view('tutor.dashboard', compact('user'));
    }
}
