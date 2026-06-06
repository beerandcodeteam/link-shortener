<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dev-only component preview gallery (Phase 1.16)
Route::get('/gallery', fn() => view('pages.gallery'))->name('gallery');

// Public redirect: must be last so it doesn't shadow named routes
Route::get('/{shortCode}', [RedirectController::class, '__invoke'])
    ->name('shorten.show');
