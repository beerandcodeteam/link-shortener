@props([
    'status' => 'active',
])

@if ($status === 'active')
    <span {{ $attributes->merge(['class' => 'badge badge-active']) }}><span class="dot"></span>Active</span>
@else
    <span {{ $attributes->merge(['class' => 'badge badge-disabled']) }}><span class="dot"></span>Disabled</span>
@endif
