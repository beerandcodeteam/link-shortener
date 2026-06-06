<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Component Gallery — Snip</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
@php
    $demoUser = (object) ['name' => 'Ada Lovelace', 'email' => 'ada@example.com'];
@endphp

<style>
    .gallery-section { padding: 40px 0; border-bottom: 1px solid var(--line-soft); }
    .gallery-section > h2 { margin: 0 0 4px; }
    .gallery-section > p { margin: 0 0 24px; }
    .row { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; }
    .stack { display: flex; flex-direction: column; gap: 18px; max-width: 420px; }
    .swatch { display: flex; flex-direction: column; gap: 6px; }
    .swatch .chip { width: 96px; height: 56px; border-radius: var(--r-md); border: 1px solid var(--line-soft); }
    .swatch code { font-size: 11px; color: var(--ink-3); }
</style>

<div class="wrap" style="padding-top: 48px; padding-bottom: 96px">
    <div class="eyebrow">Dev only · {{ url('/_gallery') }}</div>
    <h1 class="display" style="margin: 8px 0 0">Component Gallery</h1>
    <p class="body">Every Phase 1 primitive against the Snip design system.</p>

    {{-- Navbars --}}
    <section class="gallery-section">
        <h2 class="h2">NavBar</h2>
        <p class="body">Guest and authenticated states.</p>
        <div style="border: 1px solid var(--line-soft); border-radius: var(--r-md); overflow: hidden; margin-bottom: 16px">
            <x-nav-bar :user="null" style="position: static" />
        </div>
        <div style="border: 1px solid var(--line-soft); border-radius: var(--r-md); overflow: hidden">
            <x-nav-bar :user="$demoUser" style="position: static" />
        </div>
    </section>

    {{-- Colors --}}
    <section class="gallery-section">
        <h2 class="h2">Color tokens</h2>
        <p class="body">Apple-inspired palette.</p>
        <div class="row">
            @foreach (['--bg-soft', '--ink', '--ink-2', '--ink-3', '--line', '--blue', '--blue-press', '--blue-tint', '--green', '--green-tint', '--amber', '--amber-tint', '--red', '--red-tint'] as $token)
                <div class="swatch">
                    <span class="chip" style="background: var({{ $token }})"></span>
                    <code>{{ $token }}</code>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Typography --}}
    <section class="gallery-section">
        <h2 class="h2">Typography</h2>
        <p class="body">Scale and weights.</p>
        <div class="stack" style="max-width: none">
            <div class="display">Display</div>
            <div class="h1">Heading 1</div>
            <div class="h2">Heading 2</div>
            <div class="h3">Heading 3</div>
            <div class="body">Body — the quick brown fox jumps over the lazy dog.</div>
            <div class="small">Small — secondary supporting copy.</div>
            <div class="eyebrow">Eyebrow label</div>
            <div class="mono">mono · snip.sh/aZ3xK9</div>
        </div>
    </section>

    {{-- Buttons --}}
    <section class="gallery-section">
        <h2 class="h2">Button</h2>
        <p class="body">Variants, sizes, icon slot.</p>
        <div class="row" style="margin-bottom: 16px">
            <x-button variant="primary">Primary</x-button>
            <x-button variant="ghost">Ghost</x-button>
            <x-button variant="soft">Soft</x-button>
            <x-button variant="danger">Danger</x-button>
        </div>
        <div class="row" style="margin-bottom: 16px">
            <x-button variant="primary" size="sm">Small</x-button>
            <x-button variant="soft" size="sm">Small</x-button>
            <x-button variant="primary" size="lg">Large</x-button>
        </div>
        <div class="row">
            <x-button variant="primary" icon="plus">New link</x-button>
            <x-button variant="soft" icon="copy">Copy</x-button>
            <x-button variant="danger" icon="trash" size="sm">Delete</x-button>
            <button class="btn-icon" title="More"><x-icon name="chevron" /></button>
        </div>
    </section>

    {{-- Inputs --}}
    <section class="gallery-section">
        <h2 class="h2">Input</h2>
        <p class="body">Text, url, email, password, hint, error, prefix/suffix.</p>
        <div class="stack">
            <x-input label="Name" name="name" placeholder="Ada Lovelace" />
            <x-input type="url" label="Destination URL" name="url" placeholder="https://example.com/very/long/path" hint="Paste any link to shorten." />
            <x-input type="email" label="Email" name="email" placeholder="you@example.com" />
            <x-input type="password" label="Password" name="password" placeholder="••••••••" />
            <x-input label="Custom code" name="code" placeholder="my-code" error="That code is already taken." />
            <x-input label="Short link" name="short">
                <x-slot:leading>snip.sh/</x-slot:leading>
            </x-input>
            <x-input label="With trailing" name="trail" placeholder="value">
                <x-slot:trailing>.com</x-slot:trailing>
            </x-input>
        </div>
    </section>

    {{-- Select --}}
    <section class="gallery-section">
        <h2 class="h2">Select</h2>
        <p class="body">Styled native select.</p>
        <div class="stack">
            <x-select label="Sort by" name="sort" :options="['recent' => 'Most recent', 'clicks' => 'Most clicks', 'alpha' => 'Alphabetical']" selected="clicks" />
            <x-select label="Status" name="status" placeholder="Choose a status…" :options="['active' => 'Active', 'disabled' => 'Disabled']" error="Please pick one." />
        </div>
    </section>

    {{-- Checkbox --}}
    <section class="gallery-section">
        <h2 class="h2">Checkbox</h2>
        <p class="body">Label, checked, error states.</p>
        <div class="stack">
            <x-checkbox name="terms" label="I agree to the terms" />
            <x-checkbox name="news" label="Email me product updates" :checked="true" />
            <x-checkbox name="req" label="Required checkbox" error="You must accept to continue." />
        </div>
    </section>

    {{-- Radio --}}
    <section class="gallery-section">
        <h2 class="h2">Radio group</h2>
        <p class="body">Options, selection, error state.</p>
        <div class="stack">
            <x-radio name="corners" label="Corners" :options="['rounded' => 'Rounded', 'sharp' => 'Sharp']" selected="rounded" />
            <x-radio name="plan" label="Plan" :options="['free' => 'Free', 'pro' => 'Pro', 'team' => 'Team']" error="Select a plan." />
        </div>
    </section>

    {{-- Status badge --}}
    <section class="gallery-section">
        <h2 class="h2">StatusBadge</h2>
        <p class="body">Link state pill.</p>
        <div class="row">
            <x-status-badge status="active" />
            <x-status-badge status="disabled" />
        </div>
    </section>

    {{-- Copy button --}}
    <section class="gallery-section">
        <h2 class="h2">CopyButton</h2>
        <p class="body">Clipboard copy with “Copied” state.</p>
        <div class="row">
            <x-copy-button text="https://snip.sh/aZ3xK9" label="Copy link" />
            <x-copy-button text="https://snip.sh/aZ3xK9" variant="soft" />
            <span class="mono">https://snip.sh/aZ3xK9</span>
        </div>
    </section>

    {{-- Favicon + Avatar --}}
    <section class="gallery-section">
        <h2 class="h2">Favicon chip & Avatar</h2>
        <p class="body">Presentational identity marks.</p>
        <div class="row" style="margin-bottom: 20px">
            <x-favicon host="github.com" />
            <x-favicon host="x.com" />
            <x-favicon host="linkedin.com" />
            <x-favicon host="apple.com" :size="44" :radius="12" />
        </div>
        <div class="row">
            <x-avatar name="Ada Lovelace" email="ada@example.com" />
            <x-avatar name="Grace Hopper" :menu="false" />
            <span class="small">↑ click the dark circle for the menu</span>
        </div>
    </section>

    {{-- Modal --}}
    <section class="gallery-section">
        <h2 class="h2">Modal</h2>
        <p class="body">Scrim, ESC-to-close, click-outside close.</p>
        <div class="row">
            <x-button variant="primary" x-on:click="$dispatch('open-modal', 'demo')">Open modal</x-button>
            <x-button variant="danger" x-on:click="$dispatch('open-modal', 'delete')">Delete confirmation</x-button>
        </div>

        <x-modal name="demo">
            <h3 class="h3" style="margin: 0 0 8px">Create short link</h3>
            <p class="body" style="margin: 0 0 20px">Configurable width, animated entrance.</p>
            <x-input label="Destination URL" name="modal-url" placeholder="https://example.com" />
            <div class="row" style="justify-content: flex-end; margin-top: 24px">
                <x-button variant="soft" x-on:click="$dispatch('close-modal', 'demo')">Cancel</x-button>
                <x-button variant="primary" x-on:click="$dispatch('close-modal', 'demo')">Create</x-button>
            </div>
        </x-modal>

        <x-modal name="delete" width="400px">
            <h3 class="h3" style="margin: 0 0 8px">Delete this link?</h3>
            <p class="body" style="margin: 0 0 24px">This action can’t be undone.</p>
            <div class="row" style="justify-content: flex-end">
                <x-button variant="soft" x-on:click="$dispatch('close-modal', 'delete')">Cancel</x-button>
                <x-button variant="danger" x-on:click="$dispatch('close-modal', 'delete')">Delete</x-button>
            </div>
        </x-modal>
    </section>

    {{-- Toast --}}
    <section class="gallery-section" style="border-bottom: none">
        <h2 class="h2">Toast</h2>
        <p class="body">Bottom-center notification (auto-dismiss). Reload to replay.</p>
        <div x-data>
            <x-button variant="soft"
                x-on:click="
                    const el = document.createElement('div');
                    el.className = 'toast-wrap';
                    el.innerHTML = '<div class=&quot;toast&quot;><svg width=18 height=18 viewBox=&quot;0 0 24 24&quot; fill=none stroke=&quot;#4ade80&quot; stroke-width=1.7 stroke-linecap=round stroke-linejoin=round><path d=&quot;M5 12.5l4.2 4.2L19 7&quot;/></svg> Short link created</div>';
                    document.body.appendChild(el);
                    setTimeout(() => el.remove(), 2200);
                ">Trigger toast</x-button>
        </div>
    </section>
</div>

@livewireScripts
</body>
</html>
