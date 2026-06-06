<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dev-only component preview gallery (Phase 1.16)
Route::get('/gallery', fn() => view('pages.gallery'))->name('gallery');
