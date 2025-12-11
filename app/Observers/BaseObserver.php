<?php

namespace App\Observers;

use App\Services\Core\CacheService;
use Illuminate\Support\Facades\Log;

abstract class BaseObserver
{
    public function __construct(protected CacheService $cacheService)
    {}

    /**
     * Handle the model "created" event.
     */
    public function created($model): void
    {
        $this->clearCache($model, 'created');
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated($model): void
    {
        $this->clearCache($model, 'updated');
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted($model): void
    {
        $this->clearCache($model, 'deleted');
    }

    /**
     * Handle the model "restored" event.
     */
    public function restored($model): void
    {
        $this->clearCache($model, 'restored');
    }

    /**
     * Handle the model "forceDeleted" event.
     */
    public function forceDeleted($model): void
    {
        $this->clearCache($model, 'forceDeleted');
    }

    /**
     * Clear cache for this model
     */
    protected function clearCache($model, string $event): void
    {
        $prefixes = $this->getCachePrefixes($model, $event);
        
        if (empty($prefixes)) {
            return;
        }

        try {
            $this->cacheService->forgetByPrefixes($prefixes);
            
            Log::debug('Cache cleared by observer', [
                'model' => get_class($model),
                'model_id' => $model->id ?? null,
                'event' => $event,
                'prefixes' => $prefixes,
            ]);
        } catch (\Exception $e) {
            Log::warning("Failed to clear cache for " . get_class($model) . ": " . $e->getMessage());
        }
    }

    /**
     * Get cache prefixes to clear for this model
     * Override in child observers
     * 
     * @return array<string>
     */
    abstract protected function getCachePrefixes($model, string $event): array;
}
