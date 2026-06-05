<div>
    <form wire:submit="authenticate">
        <div>
            <input type="email" wire:model="email" placeholder="Email" />
        </div>
        <div>
            <input type="password" wire:model="password" placeholder="Password" />
        </div>
        @error('login') <span class="text-red-500">{{ $message }}</span> @enderror
        <button type="submit">Login</button>
    </form>
</div>
