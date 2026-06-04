<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * End the authenticated session and redirect home.
 *
 * Exposed as a POST-only route so the browser's standard CSRF
 * protection applies; a GET-based logout would be vulnerable to
 * cross-site request forgery.
 */
class LogoutController extends Controller
{
    /**
     * Handle the logout request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
