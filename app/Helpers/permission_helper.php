<?php

use Illuminate\Support\Facades\Request;

if (! function_exists('menu_active')) {
    function menu_active(string|array $patterns, string $activeClass = 'active'): string
    {
        $patterns = (array) $patterns;

        foreach ($patterns as $pattern) {
            // Support route names
            if (request()->routeIs($pattern)) {
                return $activeClass;
            }

            // Support URL path patterns
            if (Request::is($pattern)) {
                return $activeClass;
            }
        }

        return '';
    }
}
