<?php

use App\Http\Controllers\Tutor\DashboardController as TutorDashboard;
use App\Http\Controllers\Auth\LoginController;

// Tutor panel (session guard: tutor)
Route::middleware(['web','auth:tutor'])
    ->prefix('tutor')
    ->as('tutor.')
    ->group(function () {
        Route::get('/dashboard', [TutorDashboard::class, 'index'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
