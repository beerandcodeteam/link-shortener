<section class="fade-up">
    {{-- Hero --}}
    <div class="text-center" style="padding: 92px 0 8px;">
        <div class="wrap-narrow">
            <div class="inline-flex items-center gap-2 bg-[var(--color-bg-soft)] px-[14px] py-1.5 rounded-[var(--radius-pill)] text-[13px] text-[var(--color-ink-2)] mb-7">
                <x-ui.icon name="link" :size="14" class="text-[var(--color-blue)]" />
                Shorten, share, and track every link you send.
            </div>
            <h1 class="display">Make every link<br>short, smart, yours.</h1>
            <p class="body text-[21px] mt-5 max-w-[560px] mx-auto">
                Paste a long URL and get a clean short link in a tap — with click tracking, custom codes, and a dashboard that keeps it all in one place.
            </p>
        </div>
    </div>

    {{-- Shortener card --}}
    <div class="wrap-narrow mt-10">
        <form wire:submit="submit" class="card p-6 text-left" novalidate>
            <div class="flex flex-col gap-[7px]">
                <div class="flex items-stretch border rounded-[var(--radius-md)] overflow-hidden bg-white transition-[border-color,box-shadow] focus-within:border-[var(--color-blue)] focus-within:shadow-[0_0_0_4px_var(--color-blue-tint)] {{ $errors->has('original_url') ? 'border-[var(--color-red)] focus-within:shadow-[0_0_0_4px_var(--color-red-tint)]' : 'border-[var(--color-line)]' }}">
                    <span class="flex items-center px-1 pl-[15px] text-[var(--color-ink-3)] text-[17px]">
                        <x-ui.icon name="link" :size="19" />
                    </span>
                    <input
                        type="url"
                        wire:model="original_url"
                        placeholder="Paste a long link, e.g. https://example.com/very/long/path"
                        class="w-full border-0 px-[15px] py-[13px] text-[17px] text-[var(--color-ink)] placeholder:text-[var(--color-ink-3)] focus:outline-none focus:ring-0"
                        autofocus
                    >
                </div>
                @if ($errors->has('original_url'))
                    <p class="text-[12.5px] text-[var(--color-red)]">{{ $errors->first('original_url') }}</p>
                @endif
            </div>

            @if ($showCustom)
                <div class="flex flex-col gap-[7px] mt-3">
                    <div class="flex items-stretch border rounded-[var(--radius-md)] overflow-hidden bg-white transition-[border-color,box-shadow] focus-within:border-[var(--color-blue)] focus-within:shadow-[0_0_0_4px_var(--color-blue-tint)] {{ $errors->has('custom_code') ? 'border-[var(--color-red)] focus-within:shadow-[0_0_0_4px_var(--color-red-tint)]' : 'border-[var(--color-line)]' }}">
                        <span class="flex items-center px-1 pl-[15px] text-[15px] text-[var(--color-ink-2)] whitespace-nowrap">
                            {{ request()->getHost() }}/
                        </span>
                        <input
                            type="text"
                            wire:model="custom_code"
                            placeholder="custom-code (optional)"
                            class="w-full border-0 px-[15px] py-[13px] text-[16px] text-[var(--color-ink)] placeholder:text-[var(--color-ink-3)] focus:outline-none focus:ring-0"
                        >
                    </div>
                    @if ($errors->has('custom_code'))
                        <p class="text-[12.5px] text-[var(--color-red)]">{{ $errors->first('custom_code') }}</p>
                    @endif
                </div>
            @endif

            <div class="flex items-center justify-between mt-4 gap-3">
                <button
                    type="button"
                    wire:click="toggleCustom"
                    class="inline-flex items-center gap-1.5 text-[14px] text-[var(--color-blue)] hover:underline bg-transparent border-0 cursor-pointer"
                >
                    @if ($showCustom)
                        <x-ui.icon name="close" :size="15" />
                    @else
                        <x-ui.icon name="plus" :size="15" />
                    @endif
                    {{ $showCustom ? 'Remove custom code' : 'Customize the code' }}
                </button>

                <x-ui.button type="submit" variant="primary" size="lg" icon="link">
                    Shorten link
                </x-ui.button>
            </div>
        </form>
        <p class="small text-center mt-3.5 text-[var(--color-ink-3)]">
            @auth
                Your link will be saved to your dashboard.
            @else
                You'll create a free account to save it — it takes a few seconds and keeps your links forever.
            @endauth
        </p>
    </div>
</section>
