<?php

namespace App\Services;

class SafeRedirect
{
    /**
     * Return the given URL only if it stays on this site, otherwise the fallback.
     * Accepts relative paths ("/games") and absolute URLs on the current host.
     */
    public static function to(?string $url, string $fallback): string
    {
        if (! is_string($url) || $url === '') {
            return $fallback;
        }

        $url = trim($url);

        if (preg_match('/[\x00-\x1F\x7F\\\\]/', $url)) {
            return $fallback;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return $fallback;
        }

        if (! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return $fallback;
        }

        if (strcasecmp($parts['host'], request()->getHost()) !== 0) {
            return $fallback;
        }

        return $url;
    }
}
