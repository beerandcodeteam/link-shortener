<?php

namespace App\Livewire\Dashboard;

use App.Forms\LinkStoreFormObject;
use App\Models\Link;
use App\Services\ShortCodeGenerator;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateLink extends Component
{
    public string $url = '';

    public ?string $customCode = null;

    public bool $open = false;

    /** The short URL shown after successful creation. */
    public ?string $createdUrl = null;

    #[On('open-create')]
    public function openModal(): void
    {
        $this->open = true;
        $this->resetForm();
    }

    public function create(): void
    {
        $form = new LinkStoreFormObject(
            originalUrl: trim($this->url),
            customCode: trim($this->customCode) ?: null,
        );

        if (! $form->isValid()) {
            return;
        }

        $shortCode = filled($form->resolvedCode())
            ? $form->resolvedCode()
            : ShortCodeGenerator::generateUnique();

        $link = Link::create([
            'user_id' => auth()->id(),
            'original_url' => trim($this->url),
            'short_code' => $shortCode,
            'link_status_id' => 1, // Active
        ]);

        session(['flash' => 'Link created successfully']);

        $this->createdUrl = route('shorten.show', ['shortCode' => $link->short_code]);
        $this->resetForm();
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->url = '';
        $this->customCode = null;
        $this->createdUrl = null;
    }

    public function render(): array
    {
        return ['success' => $this->createdUrl ?? false];
    }
}
