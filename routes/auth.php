<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', Register::class)->name('register');
    Route::get('login', Login::class)->name('login');
});

Route::post('logout', fn () => auth()->logout() || redirect(route('home', absolute: false)))
    ->name('logout')
    ->middleware('auth');

Route::get('dashboard', fn () => view('pages.dashboard'))
    ->middleware('auth')
    ->name('dashboard');
