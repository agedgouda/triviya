<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Auth\RegisterController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/register/{userId?}', [RegisterController::class, 'show'])->name('register.prepopulated');
Route::post('register/{userId?}', [RegisterController::class, 'store'])->name('register.submit');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    // Games Routes Group
    Route::prefix('games')->group(function () {
        Route::get('/', [GameController::class, 'index'])->name('games');
        Route::get('/create', [GameController::class, 'create'])->name('games.create');
        Route::get('/{game}/edit', [GameController::class, 'edit'])->name('games.edit');
        Route::put('/{game}', [GameController::class, 'update'])->name('games.update');
        Route::get('/{game}', [GameController::class, 'show'])->name('games.show');
        Route::post('/', [GameController::class, 'store'])->name('games.store');
        Route::post('/{game}/resend-invite/{user}', [GameController::class, 'resendInvite'])
            ->name('games.resend-invite');
        Route::post('/{game}/invite', [GameController::class, 'createUserAndInvite'])->name('games.invite');
    });

});


