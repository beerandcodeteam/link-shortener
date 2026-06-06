<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\PendingShortenBridge;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Register extends Component
{
    #[Validate(['required', 'string', 'max:255'])]
    public string $name = '';

    #[Validate(['required', 'string', 'email', 'max:255', 'unique:users,email'])]
    public string $email = '';

    #[Validate(['required', 'string', 'min:8', 'confirmed'])]
    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Register the user, log them in, fulfill any pending shorten, and redirect.
     */
    public function register(PendingShortenBridge $pending): mixed
    {
        $validated = $this->validate();

        $user = User::create($validated);

        Auth::login($user);

        session()->regenerate();

        if ($link = $pending->fulfill($user)) {
            session()->flash('created_link_id', $link->id);
        }

        return $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
