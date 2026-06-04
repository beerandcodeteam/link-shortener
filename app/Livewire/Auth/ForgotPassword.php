<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * "Forgot password" request form.
 *
 * Always responds with the same generic status message — regardless of
 * whether the email matches a known account — so the form does not
 * leak which addresses are registered.
 */
#[Layout('components.layouts.guest')]
#[Title('Reset your password')]
class ForgotPassword extends Component
{
    public string $email = '';

    public ?string $statusMessage = null;

    /**
     * Dispatch a password reset link to the supplied email.
     */
    public function sendLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::broker('users')->sendResetLink([
            'email' => (string) $this->email,
        ]);

        // Always show the same generic message, regardless of whether
        // the email matched a real account, so the form does not leak
        // which addresses are registered.
        $this->statusMessage = __(
            Password::RESET_LINK_SENT
        );

        $this->reset('email');
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password');
    }
}
