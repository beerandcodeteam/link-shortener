<?php

namespace App\Livewire\Partials;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ToastRegion extends Component
{
    /** @var array<int, array{id: string, msg: string, icon?: string}> */
    public array $toasts = [];

    public function mount(): void
    {
        $this->bootFromSession();
    }

    /**
     * Pick up a flash message placed by the previous request and convert it
     * into a toast. This is invoked once on mount so a redirect-with-flash
     * surfaces a toast on the destination page.
     */
    public function bootFromSession(): void
    {
        $msg = session()->pull('toast');
        if (is_string($msg) && $msg !== '') {
            $this->push($msg, 'check');
        }
    }

    /**
     * Push a toast onto the stack. Listens for the browser-level `toast`
     * event so other Livewire components can request toasts dynamically.
     */
    public function push(string $msg, string $icon = 'check'): void
    {
        $this->toasts[] = [
            'id' => (string) str()->uuid(),
            'msg' => $msg,
            'icon' => $icon,
        ];
    }

    public function dismiss(string $id): void
    {
        $this->toasts = array_values(array_filter(
            $this->toasts,
            fn (array $t): bool => $t['id'] !== $id,
        ));
    }

    public function render(): View
    {
        return view('livewire.partials.toast-region');
    }
}
