<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class ResetPassword extends Component
{
    public string $token = '';

    #[Validate(['required', 'string', 'email'])]
    public string $email = '';

    #[Validate(['required', 'string', 'min:8', 'confirmed'])]
    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Seed the form from the reset link's token and email.
     */
    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    /**
     * Reset the user's password using the provided token.
     */
    public function resetPassword(): mixed
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        session()->flash('status', 'Your password has been reset.');

        return $this->redirectRoute('login', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.reset-password');
    }
}
