<?php

namespace App\Livewire\Auth;

use App\Forms\LinkStoreFormObject;
use App\Models\Link;
use App\Models\User;
use App\Services\ShortCodeGenerator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
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

        // Complete pending shorten flow if any.
        $this->completePendingShorten();

        session()->regenerate();

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    /** Create a link from the stashed pending payload, if any. */
    private function completePendingShorten(): void
    {
        $pending = session()->pull('pending_shorten');

        if (! is_array($pending) || empty($pending['original_url'] ?? null)) {
            return;
        }

        $form = new LinkStoreFormObject(
            originalUrl: trim($pending['original_url']),
            customCode: trim($pending['custom_code'] ?? '') ?: null,
        );

        if (! $form->isValid()) {
            return;
        }

        $shortCode = filled($form->resolvedCode())
            ? $form->resolvedCode()
            : ShortCodeGenerator::generateUnique();

        Link::create([
            'user_id'        => auth()->id(),
            'original_url'   => trim($pending['original_url']),
            'short_code'     => $shortCode,
            'link_status_id' => 1, // Active
        ]);
    }

    public function render(): array
    {
        return [
            'title' => __('Sign up'),
        ];
    }
}
