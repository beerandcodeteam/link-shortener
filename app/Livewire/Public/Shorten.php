<?php

namespace App\Livewire\Public;

use App\Livewire\Actions\CreateLinkForUser;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Public homepage "Shorten" form.
 *
 * Validates the URL (+ optional custom code) and:
 *   - for guests, stashes the payload in the session and redirects to
 *     the registration page. After a successful register/login the
 *     payload is materialised into a Link owned by the new user.
 *   - for authenticated users, creates the Link immediately and
 *     surfaces it on the dashboard via a session flash.
 */
#[Layout('components.layouts.guest')]
#[Title('Shorten a link')]
class Shorten extends Component
{
    public string $original_url = '';

    public string $custom_code = '';

    public bool $showCustom = false;

    /**
     * Toggle the "custom code" sub-field on or off.
     */
    public function toggleCustom(): void
    {
        $this->showCustom = ! $this->showCustom;
    }

    /**
     * Handle a submit from a visitor.
     *
     * Guest visitors are routed to the registration page; authenticated
     * visitors get the link created straight away.
     */
    public function submit(): void
    {
        $data = $this->validate([
            'original_url' => ['required', 'string', 'max:'.(int) config('shortener.url.max_length', 2048), 'url:http,https'],
            'custom_code' => ['nullable', 'string', 'min:'.(int) config('shortener.custom_code.min_length', 3), 'max:'.(int) config('shortener.custom_code.max_length', 32)],
        ]);

        $payload = [
            'original_url' => $data['original_url'],
            'custom_code' => $data['custom_code'] !== '' ? $data['custom_code'] : null,
        ];

        $user = Auth::guard('web')->user();

        if (! $user instanceof User) {
            session()->put('pending_shorten', $payload);

            $this->redirectRoute('register', navigate: true);

            return;
        }

        $link = app(CreateLinkForUser::class)($user, $payload);

        session()->flash('shortened_link_id', $link->getKey());
        session()->flash('toast', 'Your short link is ready.');

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.public.shorten');
    }
}
