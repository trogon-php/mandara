<?php

use Illuminate\Support\Facades\Route;
// App version API
use App\Http\Controllers\Api\ {
    AppVersionController,
    // Authentication API
    AuthController,
    BabySizeComparisonController,
    BlogApiController,
    // Home page API
    HomeController,
    // Device Token API
    DeviceTokenController,
    // Diet Plans API
    DietPlanApiController,

    // Estore
    EstoreCartController,
    EstoreController,
    EstoreOrderController,

    ProfileController,
    // Package API
    FeedController,
    GalleryImageController,
    NotificationController,
    ReelApiController,
    MandaraBookingController,
    MealPackageApiController,
    MemoryJournalController,
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
            Route::post('/update-journey', 'updateJourney');
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
        Route::get('/blogs/{id}', [BlogApiController::class, 'show']);

        // Diet Plans API
        Route::get('/diet-plans', [DietPlanApiController::class, 'index']);
        Route::get('/diet-plans/{id}', [DietPlanApiController::class, 'show']);

        // Baby Size Comparison API
        Route::get('/baby-size-comparison/{week}', [BabySizeComparisonController::class, 'getByWeek']);

        // Memory Journals API
        Route::prefix('memory-journals')->controller(MemoryJournalController::class)->group(function () {
            Route::get('/my-memories', 'myMemories');
            Route::post('/', 'store');
            // Route::get('/{id}', 'show');
            // Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::get('/date-range', 'getByDateRange');
        });

        // Booking API
        Route::prefix('mandara/booking')->controller(MandaraBookingController::class)->group(function () {
            Route::post('/', 'storeMandaraBooking');
            Route::get('/summary', 'getSummary');
            Route::post('/additional', 'storeMandaraAdditional');
            Route::post('/order/create', 'createOrder');
            Route::post('/order/complete', 'completeOrder');
        });

        // Meal Packages API
        Route::prefix('meal-packages')->controller(MealPackageApiController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'storeOrUpdate');
        });

        // -----------------Estore API ----------------------
        Route::prefix('estore')->group(function () {

            Route::get('/categories', [EstoreController::class, 'getCategories']);

            Route::prefix('products')->controller(EstoreController::class)->group(function () {
                Route::get('/', 'getProducts');
                Route::get('/{id}', 'getProduct');
            });
            Route::prefix('cart')->controller(EstoreCartController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                // Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
                Route::delete('/', 'clear');
                Route::get('/total', 'total');
                Route::get('/checkout', 'checkout');
            });
            // Orders
            Route::prefix('orders')->controller(EstoreOrderController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/create', 'store');
                Route::post('/complete', 'completeOrder');
            });
        });
        // Feedback API
        // Route::prefix('feedback')->controller(FeedbackController::class)->group(function () {
        //     Route::get('/', 'index');
        //     Route::post('/', 'store');
        // });

    });
});