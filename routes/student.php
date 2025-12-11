<?php

use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Auth\LoginController;

// Student panel (session guard: student)
Route::middleware(['web','auth:student'])
    ->prefix('student')
    ->as('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
