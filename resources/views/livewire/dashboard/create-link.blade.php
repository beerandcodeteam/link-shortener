@if ($open)
    <x-modal wire="open">
        <div class="p-6">
            @if ($success)
                {{-- Success state: show created link with copy --}}
                <h2 class="text-lg font-semibold text-ink mb-4">{{ __('Link created!') }}</h2>

                <div class="mb-4 flex items-center gap-2 rounded-md border border-line bg-[--bg-soft] px-3 py-2">
                    <code class="flex-1 truncate text-sm text-ink">{{ $success }}</code>
                    <x-copy-button value="{{ $success }}" label="Copy URL" />
                </div>

                <div class="mt-4 flex justify-end">
                    <x-button variant="primary" wire:click="$set('open', false)">Done</x-button>
                </div>
            @else
                <h2 class="text-lg font-semibold text-ink mb-4">{{ __('Create a new short link') }}</h2>

                <form wire:submit="create" class="space-y-4">
                    {{-- Original URL --}}
                    <x-input
                        type="url"
                        label="Destination URL"
                        placeholder="https://example.com/very-long-url"
                        wire:model="url"
                        :error="$errors->first('original_url')"
                        name="original_url"
                    />

                    @if ($errors->has('original_url'))
                        <p class="mt-1 text-sm text-red">{{ $errors->first('original_url') }}</p>
                    @endif

                    {{-- Custom code (optional) --}}
                    <x-input
                        type="text"
                        label="Custom code (optional)"
                        placeholder="my-link"
                        wire:model="customCode"
                        :error="$errors->first('custom_code')"
                        name="custom_code"
                    />

                    @if ($errors->has('custom_code'))
                        <p class="mt-1 text-sm text-red">{{ $errors->first('custom_code') }}</p>
                    @endif

                    <div class="flex justify-end gap-2">
                        <x-button variant="ghost" wire:click="closeModal">Cancel</x-button>
                        <x-button variant="primary" type="submit">Shorten</x-button>
                    </div>
                </form>
            @endif
        </div>
    </x-modal>

    {{-- Dispatch success toast --}}
    @if ($success)
        <script wire:ignore.self>
            setTimeout(() => location.reload(), 100);
        </script>
    @endif
@endif
