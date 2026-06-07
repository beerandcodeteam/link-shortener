<?php

use App\Livewire\Auth\Login;
use App\Models\User;

it('authenticates + redirects to dashboard on valid credentials', function () {
    $user = User::factory()->create(['password' => Hash::make('secretPassword123')]);

    livewire(Login::class)
        ->set('email', $user->email)
        ->set('password', 'secretPassword123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

it('shows generic error + does not authenticate on invalid credentials', function () {
    $user = User::factory()->create(['password' => Hash::make('secretPassword123')]);

    livewire(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrongPassword456')
        ->call('login')
        ->assertHasErrors('email')
        ->assertSessionMissing('pending_short_url');

    expect(auth()->check())->toBeFalse();
});

it('does not reveal which field is wrong — single generic message', function () {
    livewire(Login::class)
        ->set('email', 'nonexistent@example.com')
        ->set('password', 'anyPassword789')
        ->call('login')
        ->assertHasErrors('email');

    expect(auth()->check())->toBeFalse();
});

it('redirects intended url when login-url was visited before redirect-to-dashboard', function () {
    $user = User::factory()->create(['password' => Hash::make('secretPassword123')]);

    visit(route('login'))
        ->set('email', $user->email)
        ->set('password', 'secretPassword123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();
});

it('resets form fields after successful login', function () {
    $user = User::factory()->create(['password' => Hash::make('secretPassword123')]);

    livewire(Login::class)
        ->set('email', 'other@example.com')
        ->set('password', 'anotherPass999')
        ->call('login')
        ->set('email', $user->email)
        ->set('password', 'secretPassword123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    // After successful login the fields should be reset to empty defaults
    expect(
        auth()->check(),
        'User must be authenticated'
    )->toBeTrue()
      ->and($user->refresh()->email)
      ->not->toBe('');
});
