<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('guest')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';

    public function mount(): void
    {
        // Initialize inputs if necessary.
    }

    public function authenticate(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $this->addError('login', 'Invalider credentials.');
    }

    public function render(): View
    {
        return view('livewire.login');
    }
}
