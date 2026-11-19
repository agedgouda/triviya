<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanPurchaseGames
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('game.purchasing_enabled'), 404);
        abort_unless($request->user() !== null, 403);

        return $next($request);
    }
}
