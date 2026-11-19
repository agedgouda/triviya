<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->is_admin) {
            throw new NotFoundHttpException;
        }

        return $next($request);
    }
}
