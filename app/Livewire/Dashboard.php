<?php

namespace App\Livewire;

use App\Livewire\Forms\LinkForm;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Services\LinkCreator;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    public LinkForm $form;

    /**
     * Whether the create-link modal is open.
     */
    #[Url(as: 'create')]
    public bool $showCreate = false;

    /**
     * The link pending delete confirmation.
     */
    public ?int $deletingId = null;

    /**
     * The link just created via the shorten/auth flow, surfaced with a copy action.
     */
    public ?Link $createdLink = null;

    public function mount(): void
    {
        if ($id = session('created_link_id')) {
            $this->createdLink = auth()->user()->links()->find($id);
        }
    }

    /**
     * Create a new link from the dashboard form.
     */
    public function create(LinkCreator $creator): void
    {
        $this->form->validate();

        $link = $creator->create(
            auth()->user(),
            $this->form->original_url,
            $this->form->custom_code,
        );

        $this->form->reset();
        $this->showCreate = false;
        $this->resetPage();
        $this->createdLink = $link;

        $this->dispatch('toast', message: 'Short link created.');
    }

    /**
     * Toggle a link between active and disabled.
     */
    public function toggleStatus(Link $link): void
    {
        $this->authorize('update', $link);

        $target = $link->isActive ? 'disabled' : 'active';

        $link->update([
            'link_status_id' => LinkStatus::where('slug', $target)->value('id'),
        ]);

        $this->dispatch('toast', message: $target === 'active' ? 'Link enabled.' : 'Link disabled.');
    }

    /**
     * Flag a link for delete confirmation and open the modal.
     */
    public function confirmDelete(Link $link): void
    {
        $this->authorize('delete', $link);

        $this->deletingId = $link->id;
    }

    /**
     * Permanently delete the confirmed link.
     */
    public function delete(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        $link = Link::findOrFail($this->deletingId);

        $this->authorize('delete', $link);

        $link->delete();

        if ($this->createdLink?->id === $link->id) {
            $this->createdLink = null;
        }

        $this->deletingId = null;
        $this->resetPage();

        $this->dispatch('toast', message: 'Link deleted.');
    }

    public function render(): View
    {
        return view('livewire.dashboard', [
            'links' => auth()->user()
                ->links()
                ->with('linkStatus')
                ->latest()
                ->paginate(10),
        ]);
    }
}
