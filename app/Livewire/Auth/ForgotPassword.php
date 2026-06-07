<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'lowercase', 'email'],
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::ResetLinkSent) {
            session()->flash('status', __($status));
            $this->reset('email');
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render(): string
    {
        return 'livewire.auth.forgot-password';
    }
}
