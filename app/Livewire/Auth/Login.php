<?php

namespace App\Livewire\Auth;

use App\Forms\LinkStoreFormObject;
use App\Models\Link;
use App\Services\ShortCodeGenerator;
use Illuminate\Support\Facades\Auth;
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

        session()->regenerate();

        // Complete pending shorten flow if any.
        $this->completePendingShorten();

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
            'title' => __('Log in'),
        ];
    }
}
