<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (app()->environment('local')) {
    Route::view('/_gallery', 'gallery')->name('gallery');
}

/*
|--------------------------------------------------------------------------
| Public short-code redirect (catch-all)
|--------------------------------------------------------------------------
|
| Registered LAST so it never shadows named application routes. The pattern
| matches only valid short-code characters; reserved words are blocked at
| creation time, so app paths stay protected.
*/
Route::get('/{shortCode}', RedirectController::class)
    ->where('shortCode', '[A-Za-z0-9_-]+')
    ->name('redirect');
