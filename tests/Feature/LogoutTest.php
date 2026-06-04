<?php

use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('lets an authenticated user log out and redirects to the home page', function () {
    $user = User::factory()->create([
        'email' => 'alex@example.com',
        'password' => Hash::make('super-secret'),
    ]);

    $this->actingAs($user);

    expect(Auth::check())->toBeTrue();

    $response = $this->post(route('logout'));

    $response->assertRedirect(route('home'));
    expect(Auth::check())->toBeFalse();
});

it('invalidates the session on logout so the auth cookie is not reused', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $oldSessionId = session()->getId();

    $this->post(route('logout'))->assertRedirect(route('home'));

    // The session is regenerated: the old id is no longer the active one.
    expect(session()->getId())->not->toBe($oldSessionId);
    expect(Auth::check())->toBeFalse();
});

it('refuses the unauthenticated visitor with a redirect to login', function () {
    $this->post(route('logout'))->assertRedirect(route('login'));
});
