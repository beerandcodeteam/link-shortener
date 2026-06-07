<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email'    => ['required', 'string', 'lowercase', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => strtolower($this->email), 'password' => $this->password], $this->remember)) {
            $this->addError('email', __('These credentials do not match our records.'));

            return;
        }

        Session::regenerate();

        // Handle pending shorten payload if present
        $pending = session()->pull('pending_shorten');
        if (is_array($pending) && filled($pending['original_url'] ?? null)) {
            Session::put('pending_short_url', route('shorten.show', ['shortCode' => $pending['short_code']]));
        }

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    public function render(): array
    {
        return [
            'title' => __('Log in'),
        ];
    }
}
