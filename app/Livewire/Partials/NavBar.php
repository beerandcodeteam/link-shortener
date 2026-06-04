<?php

namespace App\Livewire\Partials;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NavBar extends Component
{
    public function logout(): void
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirectRoute('home', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.partials.nav-bar', [
            'user' => Auth::user(),
        ]);
    }
}
