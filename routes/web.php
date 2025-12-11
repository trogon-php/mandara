<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Tutor\DashboardController as TutorDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;

// Include all custom route files
require __DIR__.'/website.php';
require __DIR__.'/admin.php';
require __DIR__.'/tutor.php';
require __DIR__.'/student.php';


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
});

// Tutor
Route::prefix('tutor')->middleware(['auth:tutor'])->group(function () {
    Route::get('/dashboard', [TutorDashboard::class, 'index'])->name('tutor.dashboard');
});

// Student
Route::prefix('student')->middleware(['auth:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('student.dashboard');
});





