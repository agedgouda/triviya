<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectShortUrl
{
    public function handle(Request $request, Closure $next)
    {
        $shortUrl = config('app.short_url');
        $fullUrl  = config('app.url');

        // Only redirect if SHORT_URL is explicitly set and differs from APP_URL
        if ($shortUrl && $shortUrl !== $fullUrl) {
            $shortHost = parse_url($shortUrl, PHP_URL_HOST);
            $currentHost = $request->getHost();

            if ($currentHost === $shortHost) {
                $redirectTo = str_replace($shortUrl, $fullUrl, $request->fullUrl());
                return redirect()->to($redirectTo, 301);
            }
        }

        return $next($request);
    }
}
