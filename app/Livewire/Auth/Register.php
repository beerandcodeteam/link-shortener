<?php

namespace App\Livewire\Auth;

use App\Livewire\Actions\CreateLinkFromPendingPayload;
use App\Livewire\Public\Shorten;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Livewire registration form.
 *
 * Collects name, email, and password (with confirmation). After a
 * successful registration the new user is logged in and any pending
 * shorten payload stashed in the session (see {@see Shorten})
 * is converted into a Link owned by the freshly created user.
 */
#[Layout('components.layouts.guest')]
#[Title('Create your account')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle the registration submission.
     */
    public function register(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::guard('web')->login($user);
        session()->regenerate();

        $link = app(CreateLinkFromPendingPayload::class)($user);

        if ($link !== null) {
            session()->flash('shortened_link_id', $link->getKey());
            session()->flash('toast', 'Your link is ready.');
        }

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
