<?php
if (! function_exists('has_feature')) {
    function has_feature(string $feature, $default = false): bool
    {
        return config("client.features.$feature", $default);
    }
}

if (! function_exists('get_feature')) {
    function get_feature(string $feature, $default = null)
    {
        return config("client.features.$feature", $default);
    }
}

if (! function_exists('get_course_type')) {
    function get_course_type(): string
    {
        return config('client.app.home.course_type');
    }
}
