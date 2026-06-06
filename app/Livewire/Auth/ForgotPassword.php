<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class ForgotPassword extends Component
{
    #[Validate(['required', 'string', 'email'])]
    public string $email = '';

    public string $status = '';

    /**
     * Send a password reset link to the given email address.
     */
    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status !== Password::ResetLinkSent) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        $this->status = 'We have emailed your password reset link.';
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password');
    }
}
