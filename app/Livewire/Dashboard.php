<?php

namespace App\Livewire;

use App\Models\Link;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
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

    public function render(): View
    {
        return view('livewire.dashboard', [
            'links' => auth()->user()->links()->latest()->get(),
        ]);
    }
}
