<?php

namespace App\Providers;
use App\Models\User;
use App\Models\Game;
use App\Models\GameUser;

use App\Observers\GameUserObserver;
use App\Observers\UserObserver;
use App\Services\GameActions;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cookie;
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
            'flashMessage' => function () {
                return session('flashMessage');
            },
            'accountDeletedMessage' => function () {
                return Cookie::get('account_deleted_message');
            },
        ]);

        GameUser::observe(GameUserObserver::class);
        User::observe(UserObserver::class);
    }
}
