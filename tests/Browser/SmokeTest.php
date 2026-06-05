<?php

use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->seed(LookupSeeder::class);

    // The default per-IP rate-limit on /{shortCode} is 60/min. The
    // smoke test fires five or six visits per page so we can
    // comfortably stay under the cap, but we still clear any
    // state leaked from other tests in the same run.
    RateLimiter::clear('shorten:127.0.0.1');
    RateLimiter::clear('shorten-redirect:127.0.0.1');
});

/**
 * Public homepage smoke check — visits the home page and asserts no
 * console logs or JavaScript errors are produced by the design
 * system / Livewire bootstrap.
 */
it('home page loads with no JavaScript console errors', function (): void {
    visit('/')
        ->assertNoSmoke();
});

/**
 * Auth pages smoke check — visits both the register and login forms
 * and asserts no console errors fire.
 */
it('auth pages load with no JavaScript console errors', function (): void {
    visit(['/register', '/login'])
        ->assertNoSmoke();
});

/**
 * Dashboard smoke check — creates a user, walks through the
 * /login form to authenticate, and asserts no JavaScript errors
 * fire while the dashboard renders.
 */
it('dashboard loads with no JavaScript console errors', function (): void {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    // Seed at least one link so the dashboard renders the table
    // rather than the empty state.
    Link::factory()->active()->for($user)->create([
        'short_code' => 'smoke01',
        'original_url' => 'https://example.com/smoke',
    ]);

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Log in')
        ->navigate('/dashboard')
        ->assertNoSmoke();
});

/**
 * Link detail smoke check — logs in, navigates to the link detail
 * page, and asserts no JavaScript errors fire.
 */
it('link detail page loads with no JavaScript console errors', function (): void {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $link = Link::factory()->active()->for($user)->create([
        'short_code' => 'smoked1',
        'original_url' => 'https://example.com/smoke-detail',
    ]);

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Log in')
        ->navigate('/links/'.$link->getKey())
        ->assertNoSmoke();
});
