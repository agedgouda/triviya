<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a global setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::where('key', $key)->value('value') ?? $default;
    }
}
