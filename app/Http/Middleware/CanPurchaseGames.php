<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanPurchaseGames
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            abort(403, 'Game purchasing is currently disabled.');
        }

        return $next($request);
    }
}
