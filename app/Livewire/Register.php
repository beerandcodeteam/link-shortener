<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

#[Layout('guest')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        // Initialize if necessary, but for register usually empty.
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'distinct:users'],
            'password' => ['required', 'string', Password::min(8)],
            'password_confirmation' => ['required', 'same:password',],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function render(): View
    {
        return view('livewire.register');
    }
}
