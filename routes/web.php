<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
