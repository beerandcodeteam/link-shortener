<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as PasswordResetNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

beforeEach(function () {
    Notification::fake();
});

it('can render the forgot password page', function () {
    $this->withoutMiddleware()
        ->get(route('password.request'))
        ->assertOk()
        ->assertSeeLivewire(ForgotPassword::class);
});

it('can send a reset link for a known email address', function () {
    $user = User::factory()->create();

    Notification::fake();

    Livewire::test(ForgotPassword::class)
        ->set('email', $user->email)
        ->call('sendResetLink')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, PasswordResetNotification::class);
});

it('does not reveal whether email exists in the system', function () {
    Notification::fake();

    Livewire::test(ForgotPassword::class)
        ->set('email', 'nobody@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors();

    // Laravel's default password broker silently handles unknown emails,
    // so no error should appear and no notification is sent.
});

it('validates email is required', function () {
    Livewire::test(ForgotPassword::class)
        ->call('sendResetLink')
        ->assertHasErrors(['email' => 'required']);
});

it('validates email format', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', 'not-an-email')
        ->call('sendResetLink')
        ->assertHasErrors(['email' => 'email']);
});

it('can render the reset password page', function () {
    $token = str()->random(32);

    $this->withoutMiddleware()
        ->get(route('password.reset', ['token' => $token]))
        ->assertOk()
        ->assertSeeLivewire(ResetPassword::class);
});

it('can reset password with valid token and email', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasNoErrors();

    auth()->logout();

    expect(auth()->check())->toBeFalse();
    expect(auth()->attempt([
        'email' => $user->email,
        'password' => 'newpassword123',
    ]))->toBeTrue();
});

it('rejects reset with invalid token', function () {
    $user = User::factory()->create();

    Notification::fake();

    Livewire::test(ResetPassword::class, ['token' => 'invalid-token-broken'])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['email']);
});

it('validates password minimum length', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    Notification::fake();

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'min']);
});

it('validates password confirmation', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    Notification::fake();

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'different123')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'confirmed']);
});

it('redirects to login after successful password reset', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertRedirect(route('login'));
});
