<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Registry key to store all cache keys grouped by prefix
     */
    protected function getRegistryKey(): string
    {
        return 'cache_registry';
    }

    /**
     * Register a cache key under a prefix
     */
    public function registerKey(string $prefix, string $key): void
    {
        $registry = $this->getRegistry();
        $fullKey = "{$prefix}:{$key}";
        
        if (!isset($registry[$prefix])) {
            $registry[$prefix] = [];
        }
        
        if (!in_array($fullKey, $registry[$prefix])) {
            $registry[$prefix][] = $fullKey;
            $this->saveRegistry($registry);
        }
    }

    /**
     * Forget all cache keys with a given prefix
     */
    public function forgetByPrefix(string $prefix): void
    {
        $registry = $this->getRegistry();
        
        if (!isset($registry[$prefix])) {
            return;
        }

        // Delete all keys for this prefix
        foreach ($registry[$prefix] as $key) {
            Cache::forget($key);
        }

        // Remove prefix from registry
        unset($registry[$prefix]);
        $this->saveRegistry($registry);

        Log::debug("Cache cleared for prefix: {$prefix}");
    }

    /**
     * Forget multiple prefixes at once
     */
    public function forgetByPrefixes(array $prefixes): void
    {
        foreach ($prefixes as $prefix) {
            $this->forgetByPrefix($prefix);
        }
    }

    /**
     * Get the cache registry
     */
    protected function getRegistry(): array
    {
        return Cache::get($this->getRegistryKey(), []);
    }

    /**
     * Save the cache registry
     */
    protected function saveRegistry(array $registry): void
    {
        // Store registry with long TTL (30 days)
        Cache::put($this->getRegistryKey(), $registry, 2592000);
    }

    /**
     * Get all registered prefixes (for debugging)
     */
    public function getRegisteredPrefixes(): array
    {
        return array_keys($this->getRegistry());
    }

    /**
     * Get all keys for a prefix (for debugging)
     */
    public function getKeysForPrefix(string $prefix): array
    {
        $registry = $this->getRegistry();
        return $registry[$prefix] ?? [];
    }
}
