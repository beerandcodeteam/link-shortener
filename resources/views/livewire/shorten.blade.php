<div class="wrap-narrow" style="width: 100%; text-align: center">
    <h1 class="h1" style="margin-bottom: 12px">Shorten any link in seconds</h1>
    <p class="body" style="margin-bottom: 32px">Paste a long URL, get a short, shareable link.</p>

    <form wire:submit="shorten" class="card" style="padding: 24px; text-align: left; display: flex; flex-direction: column; gap: 16px">
        <x-input
            label="Long URL"
            placeholder="https://example.com/very/long/link"
            wire:model="form.original_url"
            :error="$errors->first('original_url')"
            autofocus
        />

        <x-input
            label="Custom short code (optional)"
            placeholder="my-link"
            wire:model="form.custom_code"
            :error="$errors->first('custom_code')"
        />

        <x-button type="submit" variant="primary" size="lg" wire:loading.attr="disabled">
            Shorten URL
        </x-button>
    </form>
</div>
