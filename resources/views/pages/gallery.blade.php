<x-layouts.gallery :title="'Component Gallery'">
    <div class="mx-auto max-w-5xl px-6 py-12">

        {{-- Header --}}
        <header class="mb-14">
            <a href="{{ route('home') }}" class="text-xs text-[--ink-3] hover:text-[--ink] mb-7 inline-block transition-colors">&larr; Back to home</a>
            <h1 class="text-3xl font-semibold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">Component Gallery</h1>
            <p class="mt-2.5 text-sm text-[--ink-3]">Dev-only preview of all design-system components against the Apple-inspired tokens.</p>
        </header>

        {{-- ====================== BRANDINGS ====================== --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Branding</h2>
            <div class="flex items-center gap-8 flex-wrap">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-semibold text-lg tracking-tight text-ink" aria-label="Snip home">
                    <span class="w-[26px] h-[26px] grid place-items-center">
                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.5 3v4a1 1 0 0 1-1 1h-4m13-5h5m-5 14h5m-5 4v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21.5 10.5L10.5 21.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    Snip
                </a>
                <p class="text-sm text-[--ink-3]">Logo + wordmark (reuse from nav-bar)</p>
            </div>
        </section>

        {{-- ================== TYPOGRAPHY ================= --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Typography</h2>
            <div class="space-y-4">
                <p class="text-4xl font-medium tracking-tight leading-snug display" style="font-size: 2.25rem;">Display heading &#8212; Instrument Sans</p>
                <p class="text-3xl font-semibold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">H1 heading &#8212; Let's get started</p>
                <p class="text-xl font-semibold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">H2 section title</p>
                <p class="text-base text-ink leading-relaxed body">Body copy shows how standard paragraph text renders in the App-style design system.</p>
                <p class="text-sm leading-snug" style="font-weight: 500;">Small label &#8212; eyebrow / metadata</p>
                <p class="text-xs leading-relaxed text-[--ink-3]">The smallest readable text, used for footnotes and hints.</p>
                <code class="block mt-2 px-3 py-1.5 rounded-md bg-[var(--bg)] border border-[var(--line)] text-xs font-mono">x = 'https://example.com' + '/path'</code>
            </div>
        </section>

        {{-- =================== NAVIGATION ================== --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Navigation</h2>
            <div class="flex flex-col gap-6">

                {{-- App nav (stays in its own space for demo) --}}
                <x-nav-bar mode="app" />

                {{-- Compact link row --}}
                <div class="pt-4">
                    <p class="text-xs text-[--ink-3] mb-2 font-medium uppercase tracking-wider">Nav links</p>
                    <nav class="flex items-center gap-6 text-sm">
                        <a href="#" class="text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity font-medium cursor-pointer">Shorten</a>
                        <a href="#" class="text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity cursor-pointer">Features</a>
                        <a href="#" class="text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity cursor-pointer">Pricing</a>
                        <a href="#" class="text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity font-medium cursor-pointer">Links</a>
                        <span class="shrink-0 ml-auto text-xs px-3 py-1 rounded-md bg-[--bg-soft] border border-[--line-soft] text-[--ink-2]">v{{ config('app.version', app()->version()) }}</span>
                    </nav>
                </div>
            </div>
        </section>

        {{-- ==================== BUTTONS ================== --}}
        <section class="mb-16" x-data>
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Buttons</h2>
            <div class="flex flex-wrap gap-4">

                {{-- Primary --}}
                <button type="button"
                    class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 text-sm font-medium bg-blue h-[40px] px-5 shadow-sm transition-all active:scale-[.97] hover:brightness-95 text-white">
                    Primary (default)</button>

                <button type="button" style="font-weight: 600;"
                    class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 text-sm font-medium bg-blue h-[40px] px-5 shadow-sm transition-all active:scale-[.97] hover:brightness-95 text-white">
                    Primary (bold)</button>

                <button type="button"
                    class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 text-xs font-medium bg-blue h-[28px] px-3 shadow-sm transition-all active:scale-[.97] hover:brightness-95 text-white">
                    Primary (sm)</button>

                {{-- Ghost --}}
                <button type="button"
                    class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 h-[40px] px-5 text-sm font-medium border border-transparent transition-all active:scale-[.97] hover:bg-[--bg-soft]">
                    Ghost</button>

                <button type="button" style="font-weight: 600;"
                    class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 h-[40px] px-5 text-sm font-medium border border-transparent transition-all active:scale-[.97] hover:bg-[--bg-soft]">
                    Ghost (bold)</button>

                <span class="[&]:inline-flex [&]:items-center [&]:justify-center [&]:gap-1.5 [&]:h-[28px] [&]:px-3 rounded-full text-xs font-medium border border-transparent transition-all active:scale-[.97] hover:bg-[--bg-soft] cursor-pointer inline-flex items-center justify-center gap-1.5 h-[28px] px-3 rounded-full text-xs font-medium transition-all active:scale-[.97] hover:bg-[--bg-soft] cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="w-3.5 h-3.5 shrink-0"><path d="M8 1v14M1 8h14"/></svg>
                    Ghost (with icon)</span>

                {{-- Soft --}}
                <button type="button"
                    class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 text-sm font-medium bg-[--bg-soft] border border-[--line] h-[40px] px-5 transition-all active:scale-[.97] hover:bg-[var(--line)]">
                    Soft</button>

                {{-- Danger --}}
                <button type="button"
                    class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 text-sm font-medium bg-white border border-red h-[40px] px-5 shadow-sm transition-all active:scale-[.97] hover:bg-[#ffe0e0]">
                    Danger</button>

                {{-- Disabled --}}
                <button type="button" disabled aria-disabled
                    class="cursor-not-allowed opacity-50 pointer-events-none inline-flex items-center justify-center gap-2 text-sm font-medium bg-blue h-[40px] px-5 shadow-sm rounded-full text-white">
                    Disabled</button>

                {{-- Loading --}}
                <button type="button" disabled aria-busy
                    class="cursor-wait inline-flex items-center justify-center gap-2 text-sm font-medium bg-blue h-[40px] px-5 shadow-sm rounded-full text-white transition-all active:scale-[.97]">
                    <svg class="w-4 h-4 animate-spin shrink-0" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5" opacity=".25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                    Loading</button>
            </div>
        </section>

        {{-- ================= FORM INPUTS ================ --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Form Inputs</h2>
            <div class="flex flex-wrap gap-8 items-start max-w-xl">

                {{-- Text with label --}}
                <x-input label="Username" type="text" />

                {{-- URL --}}
                <x-input label="Destination URL" type="url" value="https://example.com/path" />

                {{-- Email with error --}}
                <x-input label="Email address" type="email" value="invalid-email" error="Please enter a valid email." />

                {{-- Password with leading slot --}}
                <x-input label="Password" type="password">
                    @slot('leading')
                        <svg class="text-[--ink-3]" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="7" width="10" height="7" rx="1"/><path d="M5 7V5a2 2 0 014 0v2"/></svg>
                    @endslot
                </x-input>

                {{-- With hint --}}
                <x-input label="Website" type="url" hint="This will be the destination for your shortened link." />

                {{-- Trailing slot --}}
                <x-input label="Amount" type="text">
                    @slot('trailing')
                        <span class="text-sm text-[--ink-3]">USD</span>
                    @endslot
                </x-input>
            </div>
        </section>

        {{-- ================== SELECT & RADIO ============ --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Select &amp; Radio</h2>
            <div class="flex flex-wrap gap-8 items-start max-w-xl">

                {{-- Select with error --}}
                <x-select label="Frequency" error="Please pick a frequency.">
                    <option value="" disabled selected>Select one&#8230;</option>
                    <option value="daily">Daily digest</option>
                    <option value="weekly">Weekly summary</option>
                    <option value="monthly">Monthly recap</option>
                </x-select>

                {{-- Radio group valid --}}
                <div class="w-full md:ml-16 pt-4">
                    {{-- Radio group --}}
                    <x-radio label="Interval" hint="Choose how often you'd like to be notified." :options="[
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ]" align="start" />

                    {{-- Radio with error --}}
                    <x-radio label="Notification type" error="You must select at least one." :options="[
                        'email' => 'Email notifications',
                        'sms'   => 'SMS alerts',
                        'push'  => 'Push notifications',
                    ]" align="center" />
                </div>
            </div>
        </section>

        {{-- ================== STATUS BADGES ============== --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Status Badges</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-status-badge status="active" />
                <x-status-badge status="disabled" />
                <span class="text-sm text-[--ink-3] ml-2">&mdash; with custom label:</span>
                <x-status-badge status="active"><span>Pending Review</span></x-status-badge>
            </div>
        </section>

        {{-- ================== FAVICON & AVATAR =========== --}}

        {{-- =================== OVERLAY ================ --}}

        {{-- =================== FEEDBACK ================ --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Feedback &amp; Toast</h2>

            {{-- Static toast preview (session flash may not be present in dev-only gallery) --}}
            <div class="flex items-center gap-6 flex-wrap max-w-lg relative">
                {{ view('components.toast') }}

                @if (!session('flash'))
                    <div class="text-sm px-3 py-2 rounded-lg bg-[#d1fae5] border border-green/30 inline-flex items-center gap-2">
                        <span style="color: #4ade80">&#10003;</span>
                        Link created successfully!
                    </div>
                @endif
            </div>
        </section>

        {{-- ===================== FORMS COMBO =========== --}}
        <section class="mb-16">
            <h2 class="text-lg font-semibold text-[--ink-2] mb-5 pb-2 border-b border-[--line-soft]/50 dark:border-[#3E3E3A]">Form Combo (realistic layout)</h2>

            {{-- A realistic link creation form layout --}}
            <form class="w-full max-w-md space-y-5" onsubmit="return false">

                <x-input label="Destination URL" type="url" value="https://laravel.com/docs/13.x/routing" hint="Full destination URL (starts with https://)" />

                <div class="flex gap-4 w-full max-w-md">
                    <x-input type="text" label="Custom Code" hint="Optional &mdash; leave blank to auto-generate" placeholder="my-prefix">
                        @slot('leading')
                            <span class="text-[--ink-3]">https://snip.link/</span>
                        @endslot
                    </x-input>

                    <div class="shrink-0 pt-7">
                        <button type="button" class="inline-flex items-center gap-1.5 cursor-pointer h-[43px] px-2 rounded-full text-xs font-medium border border-line bg-white transition-all hover:bg-[--bg-soft] text-ink-3">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M16 8l-4 4 4 4"/><path d="M3 12h17"/></svg>
                            Generate</button>
                    </div>
                </div>

                {{-- Status badges + checkboxes row --}}
                <div class="flex items-start gap-6 flex-wrap pt-2">
                    <x-radio label="Link status" align="start" :options="[
                        'active' => 'Active',
                        'disabled' => 'Disabled during launch window',
                    ]" />

                    {{-- Inline checkbox demo (to be extracted as x-checkbox in Phase 1.3.4) --}}
                    <div>
                        <p class="text-xs font-medium text-[--ink-2] mb-1.5 select-none uppercase tracking-wider">Options</p>
                        @foreach ([
                            'Disable after 2026-12-31',
                            'Show QR code',
                            'Require click verification',
                        ] as $i => $text)
                        <label class="flex items-center gap-2 cursor-pointer select-none {{ $i > 0 ? 'mt-2.5' : '' }}">
                            <input type="checkbox" {{ $i === 0 ? 'checked' : '' }} class="sr-only peer">
                            <span style="
                                display: grid; place-items: center;
                                width: 20px; height: 20px;
                                flex-shrink: 0;
                                border-radius: var(--radius-sm);
                                border: 1px solid var(--line);
                                background: white;
                                transition: all .15s ease-in-out;
                            " class="peer-checked:bg-[var(--blue)] peer-checked:border-[var(--blue)] cursor-pointer bg-white"></span>
                            <span class="text-sm text-ink select-none">{{ $text }}</span>
                        </label>
                        @endforeach

                        <label class="flex items-center gap-2 cursor-pointer select-none opacity-60 mt-2.5">
                            <input type="checkbox" checked disabled class="sr-only peer">
                            <span style="
                                display: grid; place-items: center;
                                width: 20px; height: 20px;
                                flex-shrink: 0;
                                border-radius: var(--radius-sm);
                                border: 1px solid var(--line);
                                background: #f4f4f5;
                                transition: all .15s ease-in-out;
                            " class="cursor-not-allowed bg-gray-200"></span>
                            <span class="text-sm text-ink select-none">Disabled (demo)</span>
                        </label>

                        <p class="mt-3 text-xs text-[--ink-3]">Note: x-checkbox component will be built in Phase 1.3.4</p>
                    </div>
                </div>

                {{-- Actions row --}}
                <div class="flex gap-3 pt-4 items-center flex-wrap">
                    <button type="button" class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 text-sm font-medium bg-blue h-[40px] px-6 shadow-sm transition-all active:scale-[.97] hover:brightness-95 text-white">Create link</button>
                    <button type="reset" class="cursor-pointer rounded-full inline-flex items-center justify-center gap-2 h-[40px] px-6 text-sm font-medium border border-transparent transition-all active:scale-[.97] hover:bg-[--bg-soft]">Discard</button>

                    <span class="ml-auto text-xs text-[--ink-3] flex items-center">
                        Press
                        <kbd class="inline-flex items-center font-sans text-[12.5px] leading-none border-b-[1.5px] border-[#3E3E3A]/20 bg-[#F4F4F5] dark:bg-[var(--bg)] rounded px-1.5 py-1 mx-0.5">
                            <span>&#8618;</span>
                        </kbd>
                     to submit
                    </span>
                </div>

            </form>
        </section>

        {{-- =================== FOOTER ================ --}}
        <footer class="text-center text-xs text-[--ink-3] pt-8 border-t border-[--line-soft]/50 dark:border-[#3E3E3A]">
            &copy; {{ date('Y') }} Snip &mdash; Component Gallery (dev-only)<br>
            <span class="text-[12px] text-[--ink-3]/60">Built with design tokens from <code class="px-1 py-0.5 rounded bg-[var(--bg)] border border-[var(--line)] text-[11px] font-mono">docs/design-handoff/app.css</code></span>
        </footer>

    </div>
</x-layouts.gallery>
