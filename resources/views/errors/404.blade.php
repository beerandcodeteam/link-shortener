<x-layouts.guest title="Page not found">
    <div style="max-width: 480px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 16px">
        <span class="eyebrow">404</span>

        <h1 class="h1">We couldn't find that page</h1>

        <p class="body" style="color: var(--ink-2)">
            The short link or page you followed doesn't exist or may have been removed.
        </p>

        <x-button variant="primary" :href="url('/')">Go to homepage</x-button>
    </div>
</x-layouts.guest>
