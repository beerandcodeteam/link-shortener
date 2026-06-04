<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\RedirectController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard;
use App\Livewire\Public\Shorten;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/*
|--------------------------------------------------------------------------
| Public Homepage
|--------------------------------------------------------------------------
*/

Route::get('/', Shorten::class)->name('home');

/*
|--------------------------------------------------------------------------
| Auth Pages (guest only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Authenticated Area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::post('/logout', LogoutController::class)->name('logout');
});

/*
|--------------------------------------------------------------------------
| Dev Component Gallery
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/dev/components', function (): View {
        return view('dev.components');
    })->name('dev.components');
}

/*
|--------------------------------------------------------------------------
| Public Short URL Redirect (catch-all)
|--------------------------------------------------------------------------
|
| This route is registered LAST so named application routes (e.g. /login,
| /dashboard) take precedence. The "short_code" pattern is constrained to
| the URL-safe character set used by the ShortCodeGenerator service, which
| also overlaps with the reserved-words list, so app routes are safe.
|
*/

Route::get('/{shortCode}', RedirectController::class)
    ->where('shortCode', '[A-Za-z0-9_-]+')
    ->name('short-links.redirect');
