<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

if (!function_exists('get_site_logo')) {
    /**
     * Get website logo URL
     *
     * @param string $logoType
     * @return string
     */
    function get_site_logo($logoType = 'primary')
    {
        if ($logoType === 'secondary') {
            return URL::to(file_url(Config::get('app.logo_secondary')));
        }
        return URL::to(file_url(Config::get('app.logo_primary')));
    }
}

if (!function_exists('show_window_title')) {
    /**
     * Show window title as a meta tag
     *
     * @param string $page_title
     * @return void
     */
    function show_window_title($page_title)
    {
        $site_name = Config::get('app.name');
        echo "<meta name='page-title' content='" . e($page_title) . " - " . e($site_name) . "'>";
    }
}
