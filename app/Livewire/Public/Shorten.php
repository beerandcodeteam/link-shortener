<?php

namespace App\Livewire\Public;

use App\Livewire\Actions\CreateLinkForUser;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
        // Apply the "shorten" rate limiter at the top of the action so
        // abusive clients cannot farm the form by submitting invalid
        // payloads: the budget is consumed before validation runs.
        $retryAfter = $this->ensureNotRateLimited();

        if ($retryAfter > 0) {
            $this->addError(
                'original_url',
                'Too many shorten submissions. Please try again in '.$retryAfter.' seconds.'
            );

            return;
        }

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

    /**
     * Enforce the "shorten" rate limiter.
     *
     * Returns the seconds remaining on the bucket if the calling IP has
     * already exhausted its per-minute budget; otherwise records a hit
     * so the next attempt is charged against it. The error is pushed
     * onto the Livewire error bag rather than thrown because Livewire
     * tests disable Laravel's exception handler for performance —
     * only `HttpException` (and subclasses) survive to be reported.
     */
    protected function ensureNotRateLimited(): int
    {
        $key = 'shorten:'.(string) request()->ip();

        if (RateLimiter::tooManyAttempts($key, (int) config('shortener.rate_limit.shorten_per_minute', 10))) {
            return RateLimiter::availableIn($key);
        }

        RateLimiter::hit($key);

        return 0;
    }

    public function render(): View
    {
        return view('livewire.public.shorten');
    }
}
