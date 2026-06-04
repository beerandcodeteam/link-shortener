<?php

use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the forgot-password page for guests', function () {
    $this->get(route('password.request'))->assertOk();
});

it('dispatches a reset notification for a known email', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'alex@example.com']);

    Livewire::test('auth.forgot-password')
        ->set('email', 'alex@example.com')
        ->call('sendLink')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('responds with a generic status even when the email is unknown', function () {
    Notification::fake();

    Livewire::test('auth.forgot-password')
        ->set('email', 'nobody@example.com')
        ->call('sendLink')
        ->assertHasNoErrors()
        ->assertSet('statusMessage', fn ($message) => is_string($message) && $message !== '');

    // No user, no notification, but the form reports the generic
    // "we sent you a link" message and does not error.
    Notification::assertNothingSent();
});

it('requires a valid email on the forgot-password form', function () {
    Livewire::test('auth.forgot-password')
        ->set('email', 'not-an-email')
        ->call('sendLink')
        ->assertHasErrors(['email' => 'email'])
        ->assertNoRedirect();
});

it('renders the reset-password page with the token', function () {
    $user = User::factory()->create();

    $token = Password::broker('users')->createToken($user);

    $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
        ->assertOk();
});

it('lets a user set a new password using a valid token and logs them in', function () {
    $user = User::factory()->create([
        'email' => 'alex@example.com',
        'password' => Hash::make('old-password'),
    ]);

    $token = Password::broker('users')->createToken($user);

    Livewire::test('auth.reset-password', ['token' => $token])
        ->set('email', 'alex@example.com')
        ->set('password', 'brand-new-secret')
        ->set('password_confirmation', 'brand-new-secret')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect(Auth::id())->toBe($user->id);
    expect(password_verify('brand-new-secret', $user->password))->toBeTrue();
    expect(password_verify('old-password', $user->password))->toBeFalse();
});

it('rejects an invalid token', function () {
    $user = User::factory()->create(['email' => 'alex@example.com']);

    Livewire::test('auth.reset-password', ['token' => 'this-is-not-real'])
        ->set('email', 'alex@example.com')
        ->set('password', 'brand-new-secret')
        ->set('password_confirmation', 'brand-new-secret')
        ->call('resetPassword')
        ->assertHasErrors(['email'])
        ->assertNoRedirect();

    $user->refresh();
    expect(Auth::check())->toBeFalse();
    expect(password_verify('old-password-or-default', $user->password))->toBeFalse();
});

it('rejects an expired token after the configured expiry window', function () {
    $user = User::factory()->create(['email' => 'alex@example.com']);

    $token = Password::broker('users')->createToken($user);

    // Move the token row back in time past the 60-minute expiry window.
    DB::table('password_reset_tokens')
        ->where('email', $user->email)
        ->update(['created_at' => now()->subMinutes(61)]);

    Livewire::test('auth.reset-password', ['token' => $token])
        ->set('email', 'alex@example.com')
        ->set('password', 'brand-new-secret')
        ->set('password_confirmation', 'brand-new-secret')
        ->call('resetPassword')
        ->assertHasErrors(['email'])
        ->assertNoRedirect();

    expect(Auth::check())->toBeFalse();
});

it('rejects a password shorter than 8 characters on the reset form', function () {
    $user = User::factory()->create(['email' => 'alex@example.com']);

    $token = Password::broker('users')->createToken($user);

    Livewire::test('auth.reset-password', ['token' => $token])
        ->set('email', 'alex@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'min'])
        ->assertNoRedirect();
});

it('rejects a password confirmation that does not match on the reset form', function () {
    $user = User::factory()->create(['email' => 'alex@example.com']);

    $token = Password::broker('users')->createToken($user);

    Livewire::test('auth.reset-password', ['token' => $token])
        ->set('email', 'alex@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'totally-different')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'confirmed'])
        ->assertNoRedirect();
});

it('configures the password reset broker with a 60-minute expiry', function () {
    expect(config('auth.passwords.users.expire'))->toBe(60);
});
