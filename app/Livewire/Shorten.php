<?php

namespace App\Livewire;

use App\Livewire\Forms\LinkForm;
use App\Services\LinkCreator;
use App\Services\PendingShortenBridge;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Shorten extends Component
{
    public LinkForm $form;

    /**
     * Shorten the submitted URL.
     *
     * Guests have their request stashed and are bounced to registration; authenticated
     * users get the link created immediately and are sent to their dashboard.
     */
    public function shorten(PendingShortenBridge $pending, LinkCreator $creator): mixed
    {
        $this->form->validate();

        if (! auth()->check()) {
            $pending->stash($this->form->original_url, $this->form->custom_code);

            return $this->redirectRoute('register', navigate: true);
        }

        $link = $creator->create(auth()->user(), $this->form->original_url, $this->form->custom_code);

        session()->flash('created_link_id', $link->id);

        return $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.shorten');
    }
}
