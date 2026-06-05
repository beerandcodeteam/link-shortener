<button
    class="inline-flex items-center justify-center gap-2 border-none rounded-full px-5 py-2.5 transition duration-150 ease-in-out
           hover:opacity-90 active:scale-[0.97] text-sm font-medium"
    {{ $attributes->merge(['class' => 'bg-blue text-white']) }}>
    {{ $slot }}
</button>
