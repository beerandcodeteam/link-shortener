<?php

namespace App\Livewire\Auth;

use App\Livewire\Actions\CreateLinkFromPendingPayload;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Livewire login form.
 *
 * Authenticates by email + password. On bad credentials a single
 * generic error is raised so the form does not leak which field was
 * wrong. On success the user is logged in, the session is regenerated,
 * and any pending shorten payload stashed in the session is converted
 * into a real Link owned by the user.
 */
#[Layout('components.layouts.guest')]
#[Title('Log in')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * Attempt to authenticate the visitor.
     */
    public function login(): void
    {
        $data = $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt(
            ['email' => $data['email'], 'password' => $data['password']],
            (bool) $this->remember,
        )) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        $user = Auth::guard('web')->user();

        $link = $user !== null
            ? app(CreateLinkFromPendingPayload::class)($user)
            : null;

        if ($link !== null) {
            session()->flash('shortened_link_id', $link->getKey());
            session()->flash('toast', 'Your link is ready.');
        }

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
