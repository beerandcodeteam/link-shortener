<?php

namespace App\Livewire;

use App\Livewire\Actions\CreateLinkForUser;
use App\Models\Link;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Authenticated dashboard landing.
 *
 * Renders:
 *   - A "just created" card surfacing a newly-created link (if any was
 *     flashed in the session by the Register / Login / Shorten flow).
 *   - The full list of the current user's links, paginated, with a row
 *     per link showing the original URL, the short URL, click count,
 *     status badge, and creation date.
 *   - A creation form (US-4.2) so the user can shorten a new URL
 *     directly from the dashboard.
 *
 * The component also owns a small UI state machine for the delete
 * confirmation modal (US-4.5): a row-level "Delete" button sets
 * `$confirmingDelete` to the link id and a modal asks the user to
 * confirm before the actual delete action is dispatched.
 */
#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    use WithPagination;

    public string $original_url = '';

    public string $custom_code = '';

    public bool $showCustom = false;

    /** @var int|null Link id pending delete confirmation; null = no modal. */
    public ?int $confirmingDelete = null;

    /**
     * Render the dashboard.
     */
    public function render(): View
    {
        $justCreated = $this->resolveJustCreatedLink();

        return view('livewire.dashboard', [
            'justCreated' => $justCreated,
            'links' => $this->links,
        ]);
    }

    /**
     * The paginated list of links owned by the current user.
     */
    #[Computed]
    public function links(): LengthAwarePaginator
    {
        return Link::query()
            ->with('linkStatus')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    /**
     * Toggle the optional custom-code input.
     */
    public function toggleCustom(): void
    {
        $this->showCustom = ! $this->showCustom;
        if (! $this->showCustom) {
            $this->resetErrorBag('custom_code');
            $this->custom_code = '';
        }
    }

    /**
     * Handle a dashboard create-form submission.
     */
    public function createLink(): void
    {
        $payload = [
            'original_url' => trim($this->original_url),
            'custom_code' => $this->custom_code !== '' ? $this->custom_code : null,
        ];

        $user = Auth::user();

        if ($user === null) {
            abort(403);
        }

        try {
            $link = app(CreateLinkForUser::class)($user, $payload);
        } catch (ValidationException $e) {
            // Re-throw so Livewire can attach the errors to the form fields.
            throw $e;
        }

        $this->reset(['original_url', 'custom_code', 'showCustom']);
        $this->resetPage();

        session()->flash('shortened_link_id', $link->getKey());
        session()->flash('toast', 'Your short link is ready.');

        $this->redirectRoute('dashboard', navigate: true);
    }

    /**
     * Stage a link for deletion: open the confirmation modal.
     */
    public function confirmDelete(int $linkId): void
    {
        $this->authorizeDelete($linkId);
        $this->confirmingDelete = $linkId;
    }

    /**
     * Close the delete confirmation modal without deleting.
     */
    public function cancelDelete(): void
    {
        $this->confirmingDelete = null;
    }

    /**
     * Delete the link whose id is currently held in $confirmingDelete.
     */
    public function deleteConfirmed(): void
    {
        if ($this->confirmingDelete === null) {
            // Nothing to confirm — silently bail.
            return;
        }

        $linkId = (int) $this->confirmingDelete;
        $this->confirmingDelete = null;

        $this->authorizeDelete($linkId);

        $deleted = Link::query()
            ->whereKey($linkId)
            ->where('user_id', Auth::id())
            ->delete();

        if ($deleted) {
            session()->flash('toast', 'Link deleted.');
        }

        $this->redirectRoute('dashboard', navigate: true);
    }

    /**
     * Verify the current user owns the link before any destructive action.
     */
    protected function authorizeDelete(int $linkId): void
    {
        $userId = Auth::id();

        $owns = Link::query()
            ->whereKey($linkId)
            ->where('user_id', $userId)
            ->exists();

        abort_unless($owns, 403);
    }

    /**
     * Find a just-created link id from the session flash.
     */
    protected function resolveJustCreatedLink(): ?Link
    {
        $justCreatedId = session('shortened_link_id');

        if (! is_int($justCreatedId) && ! (is_string($justCreatedId) && ctype_digit($justCreatedId))) {
            return null;
        }

        return Link::query()
            ->where('user_id', Auth::id())
            ->whereKey((int) $justCreatedId)
            ->first();
    }
}
