@props([
    'message' => null,
    'icon' => 'check',
])

@php
    $message ??= session('toast') ?? session('success') ?? session('status');
@endphp

{{--
    Two delivery paths:
      • Server flash — rendered immediately when a `toast`/`success`/`status` key is flashed.
      • Livewire events — `$this->dispatch('toast', message: '...')` surfaces a toast without a full reload.
--}}
<div
    x-data="{
        show: false,
        message: @js($message),
        timer: null,
        flash(message) {
            if (! message) { return; }
            this.message = message;
            this.show = true;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.show = false, 2200);
        },
    }"
    x-init="if (message) { flash(message); }"
    x-on:toast.window="flash($event.detail?.message ?? $event.detail)"
    {{ $attributes }}
>
    <div x-show="show" x-cloak class="toast-wrap">
        <div class="toast">
            <x-icon :name="$icon" :size="18" style="color: #4ade80" />
            <span x-text="message"></span>
        </div>
    </div>
</div>
