<div>
    <x-toast />

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">{{ __('Dashboard') }}</h1>

        <x-button variant="primary" wire:click="$set('showCreate', true)">New link</x-button>
    </div>

    @if ($showCreate)
        {{-- Inline create form --}}
        <div class="mb-6 rounded-lg border border-line bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-ink mb-4">{{ __('Create a new short link') }}</h2>

            @if ($successUrl)
                {{-- Success state --}}
                <div class="flex items-center gap-2 rounded-md border border-line bg-[--bg-soft] px-3 py-2">
                    <code class="flex-1 truncate text-sm text-ink">{{ session()->pull('flash_url') }}</code>
                    <x-copy-button value="{{ session()->get('flash_url', '') }}" label="Copy URL" />
                </div>

                <div class="mt-4 flex justify-end">
                    <p class="text-sm text-green">{{ __('Link created!') }}</p>
                </div>
            @else
                <form wire:submit="create" class="space-y-4">
                    {{-- Destination URL --}}
                    <x-input
                        type="url"
                        label="Destination URL"
                        placeholder="https://example.com/very-long-url"
                        wire:model.live="form.url"
                        error="{{ $errors->first('url') }}"
                        name="url"
                    />

                    {{-- Custom code --}}
                    <x-input
                        type="text"
                        label="Custom code (optional)"
                        placeholder="my-link"
                        wire:model.live="form.custom_code"
                        error="{{ $errors->first('custom_code') }}"
                        name="custom_code"
                    />

                    <div class="flex justify-end gap-2">
                        <x-button variant="ghost" wire:click="$set('showCreate', false)">Cancel</x-button>
                        <x-button variant="primary" type="submit">Shorten</x-button>
                    </div>
                </form>
            @endif
        </div>

        @if ($successUrl)
            <script>sessionStorage.setItem('flash_url', '{{ $successUrl }}');</script>
        @endif
    @endif

    <livewire:dashboard.link-list />
</div>