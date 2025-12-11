<?php

use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\CourseController;
use App\Http\Middleware\Canonicalize; // if you added it

// Public website (no auth required). Uses 'web' middleware stack.
Route::middleware([
    'web',
    Canonicalize::class, // uncomment if you wired it
])->name('web.')->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::view('/about', 'website.pages.about')->name('about');
    Route::view('/contact', 'website.pages.contact')->name('contact');
    
    Route::view('/privacy-policy', 'website.pages.privacy-policy')->name('privacy-policy');
    Route::view('/sitemap', 'website.pages.sitemap')->name('sitemap');
    Route::view('/terms-of-service', 'website.pages.terms-of-service')->name('terms-of-service');
    Route::view('/refund-policy', 'website.pages.refund-policy')->name('refund-policy');
    Route::view('/cookie-policy', 'website.pages.cookie-policy')->name('cookie-policy');
    Route::view('/faq', 'website.pages.faq')->name('faq');

    Route::view('/blog', 'website.pages.blog')->name('blog');
    Route::view('/blog/{slug}', 'website.pages.blog-post')->name('blog.post');
    Route::view('/blog/category/{slug}', 'website.pages.blog-category')->name('blog.category');
    
    Route::view('/courses', 'website.pages.courses')->name('courses');
    Route::view('/course/{slug}', 'website.pages.course-detail')->name('course.detail');
});
