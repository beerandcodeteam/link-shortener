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

Route::get('/', function () {
    return view('welcome');
});

// ... existing login, dashboard etc routes (assumed present)

// Catch-all route at the end
Route::get('/{shortCode}', [App\Http\Controllers\RedirectController::class, 'handle'])
    ->where('shortCode', '[a-zA-Z0-9_-]+');
