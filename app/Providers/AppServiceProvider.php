<?php

namespace App\Providers;
use App\Models\User;
use App\Models\Game;

use App\Services\GameActions;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GameActions::class, function ($app) {
            return new GameActions();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-event-questions', function ($user, Game $game) {
            return $user->is_admin || $game->host()->where('user_id', $user->id)->exists();
        });

        Inertia::share([
            'stripeKey' => config('services.stripe.key'),
        ]);
    }
}
