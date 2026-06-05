<button
    x-data="{
        copied: false,
        copy() {
            navigator.clipboard.writeText({{ $value }});
            this.copied = true;
            setTimeout(() => this.copied = false, 2000)
        }
    }"
    @click="copy()"
    class="flex items-center gap-1 px-3 py-1.5 rounded-md border border-line bg-white text-sm font-medium hover:bg-gray-50"
>
    <span x-text="copied ? 'Copied' : 'Copy'"></span>
</button>
