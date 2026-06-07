<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\Link;
use App\Models\User;

function seedPendingPayload(array $payload): void
{
    session(['pending_shorten' => $payload]);
}

it('creates link after registration when pending payload exists', function () {
    seedPendingPayload([
        'original_url' => 'https://example.com/very-long-destination-url',
        'custom_code'  => null,
    ]);

    $component = livewire(Register::class);

    $component
        ->set('name', 'New User')
        ->set('email', 'newuser@example.com')
        ->set('password', 'confirmedPassword123')
        ->set('password_confirmation', 'confirmedPassword123')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();

    $createdLink = Link::where('user_id', auth()->id())->first();

    expect($createdLink)
        ->not->toBeNull()
        ->originalUrl->toBe('https://example.com/very-long-destination-url')
        ->shortCode->not->toBeEmpty();

    expect(session('pending_shorten'))->toBeNull();
});

it('uses custom code from pending payload during registration', function () {
    seedPendingPayload([
        'original_url' => 'https://example.com/custom-code-test',
        'custom_code'  => 'my-custom-code',
    ]);

    $component = livewire(Register::class);

    $component
        ->set('name', 'Custom Code User')
        ->set('email', 'customuser@example.com')
        ->set('password', 'confirmedPassword123')
        ->set('password_confirmation', 'confirmedPassword123')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    $createdLink = Link::where('user_id', auth()->id())->first();

    expect($createdLink)
        ->not->toBeNull()
        ->shortCode->toBe('my-custom-code');
});

it('clears pending payload after registration', function () {
    seedPendingPayload([
        'original_url' => 'https://example.com/clear-test',
        'custom_code'  => null,
    ]);

    livewire(Register::class)
        ->set('name', 'Clear Test User')
        ->set('email', 'cleartest@example.com')
        ->set('password', 'confirmedPassword123')
        ->set('password_confirmation', 'confirmedPassword123')
        ->call('register');

    expect(session('pending_shorten'))->toBeNull();
});

it('creates link after login when pending payload exists', function () {
    $existingUser = User::factory()->create([
        'password' => Hash::make('existingPassword123'),
    ]);

    seedPendingPayload([
        'original_url' => 'https://example.com/returning-user-url',
        'custom_code'  => null,
    ]);

    livewire(Login::class)
        ->set('email', $existingUser->email)
        ->set('password', 'existingPassword123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();

    $createdLink = Link::where('user_id', $existingUser->id)->first();

    expect($createdLink)
        ->not->toBeNull()
        ->originalUrl->toBe('https://example.com/returning-user-url');

    expect(session('pending_shorten'))->toBeNull();
});

it('uses custom code from pending payload during login', function () {
    $existingUser = User::factory()->create([
        'password' => Hash::make('loginCustomPassword123'),
    ]);

    seedPendingPayload([
        'original_url' => 'https://example.com/login-custom',
        'custom_code'  => 'returning-code',
    ]);

    livewire(Login::class)
        ->set('email', $existingUser->email)
        ->set('password', 'loginCustomPassword123')
        ->call('login');

    $createdLink = Link::where('user_id', $existingUser->id)->first();

    expect($createdLink)
        ->not->toBeNull()
        ->shortCode->toBe('returning-code');
});

it('clears pending payload after login', function () {
    $existingUser = User::factory()->create([
        'password' => Hash::make('clearLoginPassword123'),
    ]);

    seedPendingPayload([
        'original_url' => 'https://example.com/login-clear-test',
        'custom_code'  => null,
    ]);

    livewire(Login::class)
        ->set('email', $existingUser->email)
        ->set('password', 'clearLoginPassword123')
        ->call('login');

    expect(session('pending_shorten'))->toBeNull();
});

it('does not create link when no pending payload exists during registration', function () {
    User::factory()->create([
        'email' => 'nopending@example.com',
        'password' => Hash::make('nopending123'),
    ]);

    livewire(Register::class)
        ->set('name', 'No Pending')
        ->set('email', 'nopending2@example.com')
        ->set('password', 'confirmedPassword123')
        ->set('password_confirmation', 'confirmedPassword123')
        ->call('register');

    expect(Link::where('user_id', auth()->id())->exists())->toBeFalse();
});
