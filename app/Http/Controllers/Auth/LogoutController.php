<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

readonly class LogoutController
{
    public function __invoke(): RedirectResponse
    {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('home');
    }
}
