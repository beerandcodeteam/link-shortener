<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

/*
|--------------------------------------------------------------------------
| Architecture Guardrails (Pest `arch()`)
|--------------------------------------------------------------------------
|
| These tests enforce cross-cutting conventions the rest of the suite
| cannot catch on its own. They run cheaply (no DB) and act as
| tripwires against drift:
|
|   - every Eloquent model must extend the framework base class
|   - debug helpers (dd/dump/ray/var_dump) must not leak into source
|   - Livewire components must live under `app/Livewire` (the
|     framework discovers them there by default)
|
*/

it('every Eloquent model extends the framework base class', function (): void {
    $result = arch('App\Models')
        ->expect('App\Models\*')
        ->toExtend([
            Model::class,
            Authenticatable::class,
        ]);

    // Sanity check: assert the arch predicate actually ran across
    // our model files (not zero targets). This keeps the test from
    // passing "vacuously" against an empty directory.
    expect($result->targets)->not->toBeEmpty();
});

it('keeps Livewire components under the app/Livewire directory', function (): void {
    $result = arch('App\Livewire')
        ->expect('App\Livewire\*')
        // The action classes in `app/Livewire/Actions/` are not
        // Livewire components — they are plain invokable services
        // called by components. Only the *Component* classes need
        // the Component base.
        ->ignoring('App\Livewire\Actions\*')
        ->toExtend('Livewire\Component');

    expect($result->targets)->not->toBeEmpty();
});

it('does not leak any Livewire components outside app/Livewire', function (): void {
    $result = arch('App')
        ->expect('App\*')
        ->ignoring('App\Livewire\*')
        // The `app/Actions`, `app/Forms`, `app/Models`, `app/Policies`,
        // `app/Rules`, `app/Services`, `app/View` trees never house
        // Livewire components.
        ->not->toExtend('Livewire\Component');

    expect($result->targets)->not->toBeEmpty();
});

it('does not ship debug calls in production code', function (string $needle): void {
    $directories = [
        __DIR__.'/../../app',
        __DIR__.'/../../database/migrations',
        __DIR__.'/../../database/seeders',
        __DIR__.'/../../database/factories',
    ];

    $offenders = [];

    foreach ($directories as $dir) {
        if (! is_dir($dir)) {
            continue;
        }

        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iter as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            $contents = (string) file_get_contents($path);

            // Skip this very test file.
            if (str_contains($path, 'ArchTest.php')) {
                continue;
            }

            // Match as a function call: `dd(`, `dump(`, `ray(`, `var_dump(`,
            // `print_r(` — the opening paren ensures we are not catching
            // matches inside variable names or string literals.
            if (preg_match('/\b'.preg_quote($needle, '/').'\s*\(/i', $contents) === 1) {
                $offenders[] = $path;
            }
        }
    }

    expect($offenders)
        ->toBeEmpty("Found '{$needle}(' in production code: ".implode(', ', $offenders));
})->with([
    'dd',
    'dump',
    'ray',
    'var_dump',
    'print_r',
]);
