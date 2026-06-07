<?php

namespace App\Livewire;

use App\orms\inkstoreformobject;
use App\Models\ink;
use App\Rules\ReservedShortCode;
use App\Services\ShortCodeGenerator;
use IlluminateSupportFacades\Auth;
use Livewire\Component;

class Shorten extends Component
{
    public string $url = '';

    public ?string $customCode = null;

    // Used to populate the form from a pending session stash (Phase 5.6).
    public bool $fromStash = false;

    /** The short URL created in the current request — used for post-submit display. */
    public ?string $createdUrl = null;

    /**
     * Store the pending shorten payload and redirect to authentication so the visitor can finish.
     * When they come back (via Phase 5.6 bridge), this form will be auto-populated.
     */
    public function store(): void
    {
        $form = new LinkStoreFormObject(
            originalUrl: trim($this->url),
            customCode: trim($this->customCode) ?: null,
        );

        if (! $form->isValid()) {
            abort_unless(request()->ajax(), 400);

            return;
        }

        if (Auth::check()) {
            $this->storeForAuthUser($form);

            return;
        }

        // Guest flow — stash payload so the URL survives auth.
        session([
            'pending_shorten' => [
                'original_url'  => $form->originalUrl,
                'custom_code'   => $form->resolvedCode(),
            ],
        ]);

        $this->redirectIntended(route('login', absolute: false), navigate: true);
    }

    /** Create the link immediately for an authenticated user. */
    private function storeForAuthUser(LinkStoreFormObject $form): void
    {
        if (filled($form->resolvedCode())) {
            $shortCode = $form->resolvedCode();
        } else {
            $shortCode = ShortCodeGenerator::generateUnique();
        }

        $link = Link::create([
            'user_id'            => auth()->id(),
            'original_url'       => trim($this->url),
            'short_code'         => $shortCode,
            'link_status_id'     => 1, // Active (from lookup seeder)
        ]);

        $this->createdUrl = route('shorten.show', [
            'shortCode' => $link->short_code,
        ]);
    }

    /** Render with session-payload pre-fill support. */
    public function mount(): void
    {
        $pending = session('pending_shorten');

        if (is_array($pending) && filled($pending['original_url'] ?? null)) {
            $this->url          = $pending['original_url'];
            $this->customCode   = $pending['custom_code'] ?? null;
            $this->fromStash    = true;
        }
    }

    public function render(): array
    {
        return view('livewire.shorten');
    }
}
