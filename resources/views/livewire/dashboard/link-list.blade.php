@props(['links' => null, 'hasLinks' => false])

<?php $links ??= wire('links'); ?>
<?php $hasLinks ??= wire('hasLinks'); ?>

@if (!$hasLinks)
    <div class="rounded-lg bg-white border border-line-soft p-8 text-center">
        <p class="text-ink-3">{{ __('No links yet.') }}</p>
        <p class="text-ink-3 text-sm mt-1">{{ __('Paste a long URL above to create your first short link.') }}</p>
    </div>
@else
    <div class="overflow-hidden rounded-lg border border-line-soft bg-white">
        <table class="min-w-full divide-y divide-line-soft">
            <thead class="bg-[--bg-soft]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-ink-3">{{ __('Original URL') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-ink-3">{{ __('Short URL') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-ink-3">{{ __('Clicks') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-ink-3">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-ink-3">{{ __('Created') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line-soft">
                @foreach ($links as $link)
                    <tr class="hover:bg-[--bg-soft] transition-colors">
                        {{-- Original URL --}}
                        <td class="px-6 py-4">
                            <a href="{{ $link->original_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 text-sm font-medium text-[--blue] hover:underline truncate max-w-[320px]"
                               title="{{ $link->original_url }}">
                                <x-favicon-chip :url="$link->original_url" />
                                <span class="overflow-hidden">{{ str()->limit($link->original_url, 45) }}</span>
                                <svg class="size-3 text-ink-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>
                        </td>

                        {{-- Short URL + Copy --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-sm text-ink">{{ $link->short_code }}</span>
                                <x-copy-button :data="$link->shortUrl" />
                            </div>
                        </td>

                        {{-- Click count --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-2">
                            {{ number_format($link->click_count) }}
                        </td>

                        {{-- Status badge --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-status-badge :status="$link->linkStatus->slug ?? 'disabled'" />
                        </td>

                        {{-- Date --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-3">
                            {{ $link->created_at->format('M j, Y g:i A') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-line-soft">
            {{ $links->onEachSide(1)->links() }}
        </div>
    </div>
@endif
