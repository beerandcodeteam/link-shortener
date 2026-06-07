<?php

namespace App\Livewire\Pages\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public bool $showCreate = false;

    #[Validate('required|url:http,https|max:' . config('link-shortener.max_original_url_length', 2048))]
    public string $url = '';

    #[Validate('nullable|string|max:64|regex:/^[a-zA-Z0-9\-]+$/')]
    public ?string $customCode = null;

    public function create(): void
    {
        $this->validate();

        // Reserved word check.
        $customCode = trim($this->customCode) ?: null;
        if (filled($customCode)) {
            $reservedWords = config('link-shortener.reserved_words', []);
            if (in_array(strtolower($customCode), array_map('strtolower', $reservedWords), true)) {
                $this->addError('customCode', 'This custom code is reserved and cannot be used.');

                return;
            }

            // Uniqueness check.
            if (\App\Models\Link::where('short_code', $customCode)->exists()) {
                $this->addError('customCode', 'This custom code is already taken.');

                return;
            }
        }

        $shortCode = filled($customCode) ? $customCode : \App\Services\ShortCodeGenerator::generateUnique();

        \App\Models\Link::create([
            'user_id' => auth()->id(),
            'original_url' => trim($this->url),
            'short_code' => $shortCode,
            'link_status_id' => 1, // Active
        ]);

        session(['flash' => 'Link created successfully']);

        $this->showCreate = false;
        $this->url = '';
        $this->customCode = null;
    }

    public function render(): array
    {
        return ['title' => __('Dashboard')];
    }
}
