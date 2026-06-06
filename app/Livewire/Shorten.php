<?php

namespace App\Livewire;

use App\Livewire\Forms\LinkForm;
use App\Services\LinkCreator;
use App\Services\PendingShortenBridge;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Shorten extends Component
{
    /**
     * Maximum shorten submissions allowed per decay window, per client IP.
     */
    protected const MAX_ATTEMPTS = 10;

    /**
     * Window, in seconds, over which {@see self::MAX_ATTEMPTS} is enforced.
     */
    protected const DECAY_SECONDS = 60;

    public LinkForm $form;

    /**
     * Shorten the submitted URL.
     *
     * Guests have their request stashed and are bounced to registration; authenticated
     * users get the link created immediately and are sent to their dashboard. Submissions
     * are throttled per IP to protect the public endpoint from abuse (429 once exceeded).
     */
    public function shorten(PendingShortenBridge $pending, LinkCreator $creator): mixed
    {
        $this->ensureWithinRateLimit();

        $this->form->validate();

        if (! auth()->check()) {
            $pending->stash($this->form->original_url, $this->form->custom_code);

            return $this->redirectRoute('register', navigate: true);
        }

        $link = $creator->create(auth()->user(), $this->form->original_url, $this->form->custom_code);

        session()->flash('created_link_id', $link->id);

        return $this->redirectRoute('dashboard', navigate: true);
    }

    /**
     * Abort with a 429 when the client has exhausted its shorten attempts.
     */
    protected function ensureWithinRateLimit(): void
    {
        $key = 'shorten:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            abort(429, 'Too many attempts. Please slow down and try again shortly.');
        }

        RateLimiter::hit($key, self::DECAY_SECONDS);
    }

    public function render(): View
    {
        return view('livewire.shorten');
    }
}
