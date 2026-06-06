<x-layouts.guest title="Link unavailable">
    <div style="max-width: 480px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 16px">
        <x-status-badge status="disabled" />

        <h1 class="h1">This link is unavailable</h1>

        <p class="body" style="color: var(--ink-2)">
            The short link you followed has been disabled by its owner and no longer redirects.
        </p>

        <x-button variant="primary" :href="url('/')">Go to homepage</x-button>
    </div>
</x-layouts.guest>
