<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectShortUrl
{
    public function handle(Request $request, Closure $next)
    {
        $shortUrl = config('app.short_url');
        $fullUrl = config('app.url');

        // Only redirect if request is coming from the short domain
        if ($shortUrl && $fullUrl && str_starts_with($request->getSchemeAndHttpHost(), $shortUrl)) {
            $redirectTo = str_replace($shortUrl, $fullUrl, $request->fullUrl());
            return redirect()->to($redirectTo, 301);
        }

        return $next($request);
    }
}
