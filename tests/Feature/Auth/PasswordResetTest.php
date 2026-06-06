<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('dispatches a reset link notification for a known email', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'user@example.com']);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'user@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('lets a user set a new password with a valid token and then log in', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);
    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', 'user@example.com')
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
    $this->assertTrue(auth()->attempt(['email' => 'user@example.com', 'password' => 'new-password-123']));
});

it('rejects an invalid or expired token', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);

    Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
        ->set('email', 'user@example.com')
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('resetPassword')
        ->assertHasErrors(['email']);

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeFalse();
});
