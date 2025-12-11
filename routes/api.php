<?php

use Illuminate\Support\Facades\Route;
// App version API
use App\Http\Controllers\Api\ {
    AppVersionController,
    // Authentication API
    AuthController,
    BlogApiController,
    // Home page API
    HomeController,
    SearchController,
    
    // Course API
    CourseController,
    CourseUnitController,
    CourseMaterialController,
    DeviceTokenController,
    VideoLinkController,
    
    ProfileController,
    MyCourseController,
    EnrollmentController,
    
    // Package API
    PackageController,
    FeedController,
    GalleryImageController,
    NotificationController,
    LiveClassApiController,
    LiveClassController,
    ReelApiController,
    FeedbackController,
    WalletController,
    LeaderboardController,
    ExamController,
    ExamAttemptController,
    QaController,
};


Route::prefix('v1')->group(function () {

    // App version API
    Route::prefix('app-version')
    ->controller(AppVersionController::class)
    ->group(function () {
        Route::get('/', 'index');
    });

    // authentication
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/send-otp', 'sendOtp');
        Route::post('/login',    'login');
        Route::post('/verify-otp', 'verifyOtp');
        Route::post('/refresh', 'refresh');
        Route::post('/logout', 'logout');
        Route::get('/user-meta-options', 'getUserMetaOptions');
    });

    // Protected routes with JWT
    Route::middleware(['jwt.validate', 'user.active'])->group(function () {

        // registeration route
        Route::prefix('auth')->controller(AuthController::class)->group(function () {
            Route::post('/register', 'register');
            Route::post('/update-dob',    'updateDob');
            Route::post('/update-pregnancy', 'updatePregnancy');
        });
        // Device Token API
        Route::prefix('devices')->controller(DeviceTokenController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/register', 'register');
            Route::delete('/{deviceId}', 'destroy');
        });
        // Home page API
        Route::get('/home', [HomeController::class, 'index']);

        // Q & A API
        Route::prefix('qa')->controller(QaController::class)->group(function () {
            // Categories
            Route::get('/categories', 'categories');

            // Questions
            Route::get('/questions', 'getQuestions');
            Route::post('/questions', 'storeQuestion');
            Route::put('/questions/{id}', 'updateQuestion');
            Route::delete('/questions/{id}', 'deleteQuestion');

            // Answers
            Route::get('/questions/{id}/answers', 'getAnswers');
            Route::post('/questions/{id}/answers', 'storeAnswer');
            Route::delete('/answers/{id}', 'deleteAnswer');

            // Votes
            Route::post('/questions/{id}/votes', 'storeOrUpdateVote');

            // User Questions
            Route::get('/my-questions', 'getMyQuestions');
            Route::get('/my-answers', 'getMyAnswers');
        });

        // Profile API
        Route::prefix('profile')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'update');
        });

        // Feed API
        Route::get('/feeds', [FeedController::class, 'index']);

        // Gallery Images API
        Route::get('/gallery-images', [GalleryImageController::class, 'index']);

        // Notification API
        Route::get('/notifications', [NotificationController::class, 'index']);

        // Reel API
        Route::get('/reels', [ReelApiController::class, 'index']);

        // Blogs API
        Route::get('/blogs', [BlogApiController::class, 'index']);

        // Feedback API
        Route::prefix('feedback')->controller(FeedbackController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
        });

    });
});