@props([
    'message' => null,
    'icon' => 'check',
])

@php
    $message ??= session('toast') ?? session('success') ?? session('status');
@endphp

@if ($message)
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 2200)"
        x-show="show"
        x-cloak
        class="toast-wrap"
        {{ $attributes }}
    >
        <div class="toast">
            <x-icon :name="$icon" :size="18" style="color: #4ade80" />
            {{ $message }}
        </div>
    </div>
@endif
