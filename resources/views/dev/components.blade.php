<x-layouts.app title="Components">
    <div class="space-y-12">
        <header>
            <p class="eyebrow">Design system</p>
            <h1 class="h1 mt-2">Snip components</h1>
            <p class="body mt-2">Reference gallery for every Blade component shipped in Phase 1.</p>
        </header>

        <section>
            <h2 class="h2">Typography</h2>
            <div class="card mt-4 p-6 space-y-3">
                <p class="display">Display headline</p>
                <p class="h1">H1 heading</p>
                <p class="h2">H2 heading</p>
                <p class="h3">H3 heading</p>
                <p class="body">Body — Apple-inspired copy that breathes.</p>
                <p class="small">Small print for meta and helper text.</p>
                <p class="eyebrow">Eyebrow label</p>
                <p class="mono">https://snip.test/abcd1</p>
            </div>
        </section>

        <section>
            <h2 class="h2">Buttons</h2>
            <div class="card mt-4 p-6 flex flex-wrap items-center gap-3">
                <x-ui.button variant="primary">Primary</x-ui.button>
                <x-ui.button variant="ghost">Ghost</x-ui.button>
                <x-ui.button variant="soft">Soft</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
                <x-ui.button variant="primary" size="sm">Small</x-ui.button>
                <x-ui.button variant="primary" size="lg">Large</x-ui.button>
                <x-ui.button variant="primary" :icon="'plus'">With icon</x-ui.button>
                <x-ui.button size="icon" :icon="'check'" aria-label="OK" />
            </div>
        </section>

        <section>
            <h2 class="h2">Inputs</h2>
            <div class="card mt-4 p-6 grid gap-4 max-w-md">
                <x-ui.input label="Destination URL" name="url" type="url" placeholder="https://example.com/long" hint="Where visitors land." />
                <x-ui.input label="Short code" name="code" placeholder="my-link (optional)" :leading="config('app.url').'/'" />
                <x-ui.input label="Email" type="email" name="email" :error="'Email is required.'" />
                <x-ui.input type="password" name="password" label="Password" />
            </div>
        </section>

        <section>
            <h2 class="h2">Select / Checkbox / Radio</h2>
            <div class="card mt-4 p-6 grid gap-4 max-w-md">
                <x-ui.select label="Domain" name="domain">
                    <option>snip.test</option>
                    <option>go.acme.com</option>
                </x-ui.select>
                <x-ui.checkbox label="Track clicks" name="track" :checked="true" />
                <x-ui.radio label="Visibility" name="vis" :value="'public'" :options="[['value' => 'public', 'label' => 'Public'], ['value' => 'private', 'label' => 'Private']]" />
            </div>
        </section>

        <section>
            <h2 class="h2">Status / Copy / Favicon / Avatar</h2>
            <div class="card mt-4 p-6 flex flex-wrap items-center gap-4">
                <x-ui.status-badge status="active" />
                <x-ui.status-badge status="disabled" />
                <x-ui.copy-button text="https://snip.test/abc" label="Copy" />
                <x-ui.copy-button text="https://snip.test/abc" />
                <x-ui.favicon host="github.com" />
                <x-ui.favicon host="vercel.com" :size="44" :radius="12" />
                <x-ui.avatar :name="'Ada Lovelace'" />
            </div>
        </section>

        <section>
            <h2 class="h2">Modal</h2>
            <div class="card mt-4 p-6" x-data="{ open: false }">
                <x-ui.button variant="primary" @click="open = true">Open modal</x-ui.button>
                <x-ui.modal :show="true" width="420">
                    <h3 class="h3">Delete link?</h3>
                    <p class="small mt-2">This action can't be undone.</p>
                    <div class="mt-6 flex justify-end gap-2">
                        <x-ui.button variant="ghost" @click="open = false">Cancel</x-ui.button>
                        <x-ui.button variant="danger" @click="open = false">Delete</x-ui.button>
                    </div>
                </x-ui.modal>
            </div>
        </section>
    </div>
</x-layouts.app>
