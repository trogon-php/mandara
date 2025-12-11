<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


if (!function_exists('get_user_image')) {
    /**
     * Get current user's profile image
     *
     * @return string
     */
    function get_user_image()
    {
        $user = Auth::user();
        if (!$user) {
            return '';
        }
        
        // Return user's profile image path
        return $user->profile_image ?? '';
    }
}

if (!function_exists('get_user_name')) {
    /**
     * Get current user's name
     *
     * @return string
     */
    function get_user_name()
    {
        $user = Auth::user();
        if (!$user) {
            return 'Guest';
        }
        
        // Return user's name
        return $user->name ?? $user->email ?? 'User';
    }
}

if (!function_exists('get_role_title')) {
    /**
     * Get current user's role title
     *
     * @return string
     */
    function get_role_title()
    {
        $user = Auth::user();
        if (!$user) {
            return 'Guest';
        }
        
        // Return user's role title
        return $user->role ?? 'User';
    }

    /**
     * get api user
     */
    if (!function_exists('authUser')) {
        function authUser($api = true)
        {
            if ($api) {
                return app('authUser');
            }
            return null;
        }
    }
}

