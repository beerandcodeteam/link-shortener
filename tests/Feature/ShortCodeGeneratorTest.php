<?php

use App\Models\Link;
use App\Services\ShortCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates a code matching the configured charset and length', function () {
    $length = (int) config('links.short_code.length');
    $alphabet = (string) config('links.short_code.alphabet');

    $code = app(ShortCodeGenerator::class)->generate();

    expect(strlen($code))->toBe($length);
    expect($code)->toMatch('/^['.preg_quote($alphabet, '/').']+$/');
});

it('regenerates to a unique value when a collision occurs', function () {
    config()->set('links.short_code.alphabet', 'ab');
    config()->set('links.short_code.length', 1);

    Link::factory()->create(['short_code' => 'a']);

    $code = app(ShortCodeGenerator::class)->generate();

    expect($code)->toBe('b');
});

it('produces unique codes across many iterations', function () {
    $generator = app(ShortCodeGenerator::class);

    $codes = [];

    for ($i = 0; $i < 50; $i++) {
        $code = $generator->generate();
        Link::factory()->create(['short_code' => $code]);
        $codes[] = $code;
    }

    expect($codes)->toHaveCount(50);
    expect(array_unique($codes))->toHaveCount(50);
});

it('throws when no unique code can be produced within the attempt limit', function () {
    config()->set('links.short_code.alphabet', 'a');
    config()->set('links.short_code.length', 1);
    config()->set('links.short_code.max_collision_attempts', 3);

    Link::factory()->create(['short_code' => 'a']);

    expect(fn () => app(ShortCodeGenerator::class)->generate())
        ->toThrow(RuntimeException::class);
});
