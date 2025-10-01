<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HideServerHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Remove headers that are safe to remove for all requests
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->remove('X-AspNet-Version');
        $response->headers->remove('X-AspNetMvc-Version');

        // Remove X-Inertia only for public/external endpoints
        // For example, all routes under /api/*
        if ($request->is('api/*')) {
            $response->headers->remove('X-Inertia');
        }

        return $response;
    }
}
