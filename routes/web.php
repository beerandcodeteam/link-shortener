<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\RedirectController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard;
use App\Livewire\LinkDetail;
use App\Livewire\Shorten;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Shorten::class)->name('home');

if (app()->environment('local')) {
    Route::view('/_gallery', 'gallery')->name('gallery');
}

/*
|--------------------------------------------------------------------------
| Guest authentication routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::livewire('/register', Register::class)->name('register');
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::livewire('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('/links/{link}', LinkDetail::class)->name('links.show');
    Route::post('/logout', LogoutController::class)->name('logout');
});

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
