<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Admin\{
    // Dashboard
    DashboardController,

    // User & Roles
    RoleController,
    LoginAttemptController,

    //  Feeds 
    FeedCategoryController,
    FeedController,

    //  Coupons
    CouponController,
    CouponPackageController,
    CouponUserController,

    //  Orders
    OrderController,

    //  Payments
    PaymentController,

    //  Media 
    BannerController,
    BlogController,
    ClientController,
    NurseController,
    DoctorController,
    
    AttendantController,
    EstoreDeliveryStaffController,
    FoodDeliveryStaffController,
    FrontOfficeController,
    ReelCategoryController,
    ReelController,
    GalleryAlbumController,
    GalleryImageController,
    //  Notifications 
    NotificationController,

    //  Feedback
    FeedbackController,

    //  Integrations
    ClientCredentialController,
    CottageCategoryController,
    CottageController,
    CottagePackageController,
    DietPlanController,
    MealPackageController,
    MediaController,
    EstoreCategoryController,
    EstoreProductController,
    MandaraBookingController,
    MandaraPaymentController,
    MandaraBookingQuestionsController,
    AmenityController,
    EstoreOrderController,
    //  Reports
    ReferralReportController,
    SlugController,
    TopReferrersController
};



// Login (shared controller)
Route::middleware(['web','guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.post');
});

// Admin panel (session guard: admin)
Route::middleware(['web','auth:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Reel Categories routes
        Route::prefix('reel-categories')->name('reel-categories.')->controller(ReelCategoryController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{reel-category}/clone', 'cloneItem')->name('clone');
        });

        Route::resource('reel-categories', ReelCategoryController::class);

        // Reels routes
        Route::prefix('reels')->name('reels.')->controller(ReelController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{reel}/clone', 'cloneItem')->name('clone');
        });

        Route::resource('reels', ReelController::class);
        
        // Feed Categories routes
        Route::prefix('feed-categories')->name('feed-categories.')->controller(FeedCategoryController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{feedCategory}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('feed-categories', FeedCategoryController::class);

        // Feeds routes
        Route::prefix('feeds')->name('feeds.')->controller(FeedController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{feed}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('feeds', FeedController::class);

        // Roles routes
        Route::prefix('roles')->name('roles.')->controller(RoleController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{roles}/clone', 'cloneItem')->name('clone');
        });

        Route::resource('roles', RoleController::class);

        // Notifications routes
        Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{review}/clone', 'cloneItem')->name('clone');
        });

        Route::resource('notifications', NotificationController::class);

        // Banners routes
        Route::prefix('banners')->name('banners.')->controller(BannerController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{banner}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('banners', BannerController::class);

        // Gallery Albums routes
        Route::prefix('gallery-albums')->name('gallery-albums.')->controller(GalleryAlbumController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{gallery_album}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('gallery-albums', GalleryAlbumController::class);

        // Gallery Images routes
        Route::prefix('gallery-images')->name('gallery-images.')->controller(GalleryImageController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{gallery_image}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('gallery-images', GalleryImageController::class);

        // Clients routes
        Route::prefix('clients')->name('clients.')->controller(ClientController::class)->group(function () {
            Route::get('select2-ajax-options', 'getSelect2AjaxOptions')->name('select2-ajax-options');
        });
        Route::resource('clients', ClientController::class);
        // Nurses routes
        Route::prefix('nurses')->name('nurses.')->controller(NurseController::class)->group(function () {
            Route::get('select2-ajax-options', 'getSelect2AjaxOptions')->name('select2-ajax-options');
        });
        Route::resource('nurses', NurseController::class);
        // Doctors routes
        Route::prefix('doctors')->name('doctors.')->controller(DoctorController::class)->group(function () {
            Route::get('select2-ajax-options', 'getSelect2AjaxOptions')->name('select2-ajax-options');
        });
        Route::resource('doctors', DoctorController::class);
        // Kitchen Staff routes
        
        // Attendants routes
        Route::prefix('attendants')->name('attendants.')->controller(AttendantController::class)->group(function () {
            Route::get('select2-ajax-options', 'getSelect2AjaxOptions')->name('select2-ajax-options');
        });
        Route::resource('attendants', AttendantController::class);
        // Delivery Agent Estore routes
        Route::prefix('estore-delivery-staff')->name('estore-delivery-staff.')->controller(EstoreDeliveryStaffController::class)->group(function () {
            Route::get('select2-ajax-options', 'getSelect2AjaxOptions')->name('select2-ajax-options');
        });
        Route::resource('estore-delivery-staff', EstoreDeliveryStaffController::class);
        // Food Delivery Staff routes
        Route::prefix('food-delivery-staff')->name('food-delivery-staff.')->controller(FoodDeliveryStaffController::class)->group(function () {
            Route::get('select2-ajax-options', 'getSelect2AjaxOptions')->name('select2-ajax-options');
        });
        Route::resource('food-delivery-staff', FoodDeliveryStaffController::class);
        // Front Office routes
        Route::prefix('front-office')->name('front-office.')->controller(FrontOfficeController::class)->group(function () {
            Route::get('select2-ajax-options', 'getSelect2AjaxOptions')->name('select2-ajax-options');
        });
        Route::resource('front-office', FrontOfficeController::class);
       
        // Cottage Packages routes
        Route::prefix('cottage-packages')->name('cottage-packages.')->controller(CottagePackageController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{cottage-package}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('cottage-packages', CottagePackageController::class);

        // Meal Packages routes
        Route::prefix('meal-packages')->name('meal-packages.')->controller(MealPackageController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{meal-package}/clone', 'cloneItem')->name('clone');
        });

        Route::resource('meal-packages', MealPackageController::class);
        
        // Coupons routes
        Route::prefix('coupons')->name('coupons.')->controller(CouponController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{coupon}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::get('/{coupon}/usage-stats', 'usageStats')->name('usage-stats');
        });
        Route::resource('coupons', CouponController::class);

        // Coupon Packages routes
        Route::prefix('coupons/{coupon}/packages')->name('coupons.packages.')->controller(CouponPackageController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{package}', 'destroy')->name('destroy');
            Route::post('bulk-detach', 'bulkDetach')->name('bulk-detach');
            Route::put('sync', 'sync')->name('sync');
            Route::get('available', 'getAvailablePackages')->name('available');
        });

        // Coupon Users routes
        Route::prefix('coupons/{coupon}/users')->name('coupons.users.')->controller(CouponUserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{user}', 'destroy')->name('destroy');
            Route::post('bulk-detach', 'bulkDetach')->name('bulk-detach');
            Route::put('sync', 'sync')->name('sync');
            Route::get('available', 'getAvailableUsers')->name('available');
            Route::get('/{user}/usage-stats', 'usageStats')->name('usage-stats');
        });

        // Orders routes
        Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{order}/update-status', 'updateStatus')->name('update-status');
            Route::get('stats', 'stats')->name('stats');
            Route::get('by-status/{status}', 'getByStatus')->name('by-status');
            Route::get('{order}/details', 'getDetails')->name('details');
            Route::get('{order}/payments', 'getPayments')->name('payments');
        });
        Route::resource('orders', OrderController::class);

        // Package details route for order form
        // Route::get('packages/{package}/details', [PackageController::class, 'getDetails'])->name('packages.details');
        
        // Coupon validation route for order form
        Route::get('coupons/validate/{code}', [CouponController::class, 'validateCode'])->name('coupons.validate');

        // Payment routes
        Route::prefix('payments')->name('payments.')->controller(PaymentController::class)->group(function () {
            Route::get('stats', 'stats')->name('stats');
            Route::get('by-status/{status}', 'getByStatus')->name('by-status');
            Route::post('{payment}/update-status', 'updateStatus')->name('update-status');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
        });
        Route::resource('payments', PaymentController::class);

        // Login Attempts routes
        Route::prefix('login-attempts')->name('login-attempts.')->controller(LoginAttemptController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
        });
        Route::resource('login-attempts', LoginAttemptController::class)->only(['index', 'destroy']);

        // Cottage Categories routes
        Route::prefix('cottage-categories')->name('cottage_categories.')->controller(CottageCategoryController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{cottageCategory}/clone', 'cloneItem')->name('clone');
        });
        // Cottages routes
        Route::prefix('cottages')->name('cottages.')->controller(CottageController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{cottage}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('cottages', CottageController::class);
        
        Route::resource('cottage-categories', CottageCategoryController::class);
        // Blogs routes
        Route::prefix('blogs')->name('blogs.')->controller(BlogController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{blog}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('blogs', BlogController::class);

        // Estore Categories routes
        Route::prefix('estore-categories')->name('estore_categories.')->controller(EstoreCategoryController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{estoreCategory}/clone', 'cloneItem')->name('clone');
        });

        // Estore Products routes
        Route::prefix('estore-products')->name('estore-products.')->controller(EstoreProductController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{estoreProduct}/clone', 'cloneItem')->name('clone');
        });
        Route::post('estore-orders/{id}/assign', [EstoreOrderController::class, 'assignOrder'])->name('estore-orders.assign');
        Route::resource('estore-products', EstoreProductController::class);
        Route::resource('estore-categories', EstoreCategoryController::class);
        Route::resource('estore-orders', EstoreOrderController::class);

        //Front Office Stay Bookings routes
        Route::prefix('mandara-bookings')->name('mandara-bookings.')->controller(MandaraBookingController::class)
        ->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            //check existing booking ajax function
            Route::get('check-existing','checkExisting')->name('check-existing');
            //payment view
            Route::get('{booking}/payment', 'paymentView')->name('payment-view');
            Route::post('{booking}/payment','storePayment')->name('payment-store');
            //additional details view
            Route::get('{booking}/additional-details', 'additionalDetails')
            ->name('additional-details');
            Route::post('{booking}/additional-details', 'storeAdditionalDetails')
            ->name('additional-details-store');
            Route::post('{booking}/approve', 'approve')->name('approve');
            Route::post('{booking}/reject', 'reject')->name('reject');
            Route::get('{booking}/review','review')->name('review');

        });
        Route::resource('mandara-bookings', MandaraBookingController::class);
        
        // Mandara Payments routes
        Route::prefix('mandara-payments')->name('mandara-payments.')->controller(MandaraPaymentController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
        });
        Route::resource('mandara-payments', MandaraPaymentController::class);
        // Mandara Booking Questions routes
        Route::prefix('mandara-booking-questions')->name('mandara-booking-questions.')->controller(MandaraBookingQuestionsController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
        });
        Route::resource('mandara-booking-questions', MandaraBookingQuestionsController::class);
        //Amenities routes
        Route::prefix('amenities')->name('amenities.')->controller(AmenityController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
        });
        Route::resource('amenities', AmenityController::class);

        // Diet Plans routes
        Route::prefix('diet-plans')->name('diet-plans.')->controller(DietPlanController::class)->group(function () {
            Route::get('sort', 'sortView')->name('sort.view');
            Route::post('sort', 'sortUpdate')->name('sort.update');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/{diet-plan}/clone', 'cloneItem')->name('clone');
        });
        Route::resource('diet-plans', DietPlanController::class);
        
        // Feedback routes
        Route::prefix('feedbacks')->name('feedbacks.')->controller(FeedbackController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('bulk-update-status', 'bulkUpdateStatus')->name('bulk-update-status');
            Route::post('/{feedback}/update-status', 'updateStatus')->name('update-status');
            Route::get('statistics', 'statistics')->name('statistics');
        });
        Route::resource('feedbacks', FeedbackController::class)->only(['index', 'show', 'destroy']);
        
        // Media Library routes
        Route::prefix('media')->name('media.')->controller(MediaController::class)->group(function () {
            Route::post('store', 'store')->name('store');
            Route::get('{id}/url', 'getUrl')->name('url');
            Route::post('url-by-path', 'getUrlByPath')->name('url-by-path');
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
        });
        Route::resource('media', MediaController::class);
        
        // Client Credentials routes
        Route::prefix('client-credentials')->name('client-credentials.')->controller(ClientCredentialController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::get('by-provider', 'getByProvider')->name('by-provider');
            Route::get('by-credential-key', 'getByCredentialKey')->name('by-credential-key');
        });
        Route::resource('client-credentials', ClientCredentialController::class);

        // Referral Report routes
        Route::prefix('reports/referrals')->name('reports.referrals.')->controller(ReferralReportController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{referral}/edit', 'edit')->name('edit');
            Route::put('/{referral}', 'update')->name('update');
            Route::delete('/{referral}', 'destroy')->name('destroy');
        });

        // Top Referrers Report routes
        Route::prefix('reports/top-referrers')->name('reports.top-referrers.')->controller(TopReferrersController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::post('/slug/check', [SlugController::class, 'check'])->name('slug.check');
    });
