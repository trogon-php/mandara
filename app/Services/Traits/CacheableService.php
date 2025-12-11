<?php

namespace App\Services\Traits;

use App\Services\Core\CacheService;
use Illuminate\Support\Facades\Cache;

trait CacheableService
{
    /**
     * Whether caching is enabled (skip in local/dev)
     */
    protected function isCacheEnabled(): bool
    {
        return false;
        // return ! app()->environment('local', 'testing');
    }

    /**
     * Get cache TTL (seconds) - can be overridden in service
     */
    protected function getCacheTtl(): int
    {
        return $this->cacheTtl ?? 300;
    }

    /**
     * Get cache prefix - can be overridden in service
     */
    protected function getCachePrefix(): string
    {
        return $this->cachePrefix ?? strtolower(class_basename(static::class));
    }

    /**
     * Build a cache key with prefix
     */
    protected function cacheKey(string $suffix): string
    {
        return "{$this->getCachePrefix()}:{$suffix}";
    }

    /**
     * Store and return cached data
     */
    protected function remember(string $key, \Closure $callback, ?int $ttl = null)
    {
        if (! $this->isCacheEnabled()) {
            return $callback();
        }

        $fullKey = $this->cacheKey($key);
        $ttl = $ttl ?? $this->getCacheTtl();

        $this->getCacheService()->registerKey($this->getCachePrefix(), $key);
        
        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Put a value in cache
     */
    protected function put(string $key, mixed $value, ?int $ttl = null): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        $fullKey = $this->cacheKey($key);
        $ttl = $ttl ?? $this->getCacheTtl();

        $this->getCacheService()->registerKey($this->getCachePrefix(), $key);
        
        Cache::put($fullKey, $value, $ttl);
    }

    /**
     * Forget a specific cache key
     */
    protected function forget(string $key): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        Cache::forget($this->cacheKey($key));
    }

    /**
     * Clear all cache entries for this service
     */
    public function clearCache(): void
    {
        if ($this->isCacheEnabled()) {
            $this->getCacheService()->forgetByPrefix($this->getCachePrefix());
        }
    }

    /**
     * Get CacheService instance
     */
    protected function getCacheService(): CacheService
    {
        return app(CacheService::class);
    }
}
