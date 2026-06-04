<?php

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
