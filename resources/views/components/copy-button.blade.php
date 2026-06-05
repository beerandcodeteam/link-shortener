<button
    x-data="{ copied: false }"
    @click="copied = true; $el.innerText='Copied'; setTimeout(() => { $el.innerText='Copy' }, 2000)"
    class="flex items-center gap-1 px-3 py-1.5 rounded-md border border-line bg-white text-sm font-medium hover:bg-gray-50"
>
    <span x-text="copied ? 'Copied' : 'Copy'"></span>
</button>
