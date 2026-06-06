<?php

namespace App\Livewire\Auth;

use App\Services\PendingShortenBridge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    #[Validate(['required', 'string', 'email'])]
    public string $email = '';

    #[Validate(['required', 'string'])]
    public string $password = '';

    public bool $remember = false;

    /**
     * Authenticate the user, fulfill any pending shorten, and redirect.
     */
    public function login(PendingShortenBridge $pending): mixed
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        $user = Auth::user();

        if ($link = $pending->fulfill($user)) {
            session()->flash('created_link_id', $link->id);
        }

        return $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
