<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $this->name,
            'email'    => strtolower($this->email),
            'password' => $this->password,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Handle pending shorten payload from homepage before registration
        $pending = session()->pull('pending_shorten');
        if (is_array($pending) && filled($pending['original_url'] ?? null)) {
            Session::put('pending_short_url', route('shorten.show', ['shortCode' => $pending['short_code']]));
        }

        session()->regenerate();

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    public function render(): array
    {
        return [
            'title' => __('Sign up'),
        ];
    }
}
