<?php

namespace App\Providers;

use App\Services\Core\CacheService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Map polymorphic relationships for video links
        Relation::morphMap([
            'feed' => \App\Models\Feed::class,
            'reel' => \App\Models\Reel::class,
            'banner' => \App\Models\Banner::class,
        ]);
        // Register observers
        $this->registerObservers();
    }
    /**
     * Register all model observers
     */
    protected function registerObservers(): void
    {
        
    }
}
