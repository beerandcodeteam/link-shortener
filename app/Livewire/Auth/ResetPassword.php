<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Final step of the password-reset flow.
 *
 * Receives a signed reset token in the URL, validates the new password
 * (≥8 chars + confirmation) and, on success, updates the user's
 * password, logs them in, and redirects to the dashboard.
 */
#[Layout('components.layouts.guest')]
#[Title('Set a new password')]
class ResetPassword extends Component
{
    #[Url]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Hydrate the component with token + email hints from the query
     * string so the form is pre-filled for the user.
     */
    public function mount(): void
    {
        $this->email = (string) request()->input('email', $this->email);
    }

    /**
     * Reset the password and log the user in.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker('users')->reset(
            [
                'email' => (string) $this->email,
                'password' => (string) $this->password,
                'password_confirmation' => (string) $this->password_confirmation,
                'token' => (string) $this->token,
            ],
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));

                Auth::guard('web')->login($user);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        session()->regenerate();

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.reset-password');
    }
}
