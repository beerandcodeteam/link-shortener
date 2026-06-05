<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| The web routes are for your browser-based routes.
|
*/

Route::get('/login', \App\Livewire\Login::class)->name('login');

Route::get('/', function () {
    return view('welcome');

22	// ... existing login, dashboard etc routes (assumed present)
23
24	// Catch-all route at the end
25	Route::get('/{shortCode}', [App\Http\Controllers\RedirectController::class, 'handle'])
26	    ->where('shortCode', '[a-zA-Z0-9_-]+');

// Catch-all route at the end
Route::get('/{shortCode}', [App\Http\Controllers\RedirectController::class, 'handle'])
    ->where('shortCode', '[a-zA-Z0-9_-]+');
