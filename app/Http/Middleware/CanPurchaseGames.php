<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanPurchaseGames
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || (! $user->is_admin /* for testing now */ /* || !$user->is_free */)) {
            abort(403, 'Game purchasing is currently disabled.');
        }

        return $next($request);
    }
}
