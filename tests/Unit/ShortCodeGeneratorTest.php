<?php

use App\Models\Link;
use App\Services\ShortCodeGenerator;
use Database\Seeders\LookupSeeder;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('generates a code that matches the configured length and alphabet', function () {
    config()->set('shortener.short_code.length', 8);
    config()->set('shortener.short_code.alphabet', 'abcdefghijkmnpqrstuvwxyz23456789');

    $generator = new ShortCodeGenerator;
    $code = $generator->generate();

    expect($code)->toHaveLength(8);
    expect($code)->toMatch('/^[abcdefghijkmnpqrstuvwxyz23456789]{8}$/');
});

it('honors a per-call length override', function () {
    config()->set('shortener.short_code.length', 7);
    config()->set('shortener.short_code.alphabet', 'abcdefghijkmnpqrstuvwxyz23456789');

    $generator = new ShortCodeGenerator;

    $short = $generator->generate(4);
    $long = $generator->generate(16);

    expect($short)->toHaveLength(4);
    expect($long)->toHaveLength(16);
    expect($short)->toMatch('/^[abcdefghijkmnpqrstuvwxyz23456789]{4}$/');
    expect($long)->toMatch('/^[abcdefghijkmnpqrstuvwxyz23456789]{16}$/');
});

it('regenerates a unique value when the first attempt collides', function () {
    config()->set('shortener.short_code.length', 7);
    config()->set('shortener.short_code.alphabet', 'abcdefghijkmnpqrstuvwxyz23456789');
    config()->set('shortener.short_code.max_attempts', 5);

    $existing = 'abc2345';
    Link::factory()->create(['short_code' => $existing]);

    $generator = new ShortCodeGenerator;
    $code = $generator->generate();

    expect($code)->not->toBe($existing);
    expect($code)->toHaveLength(7);
    expect(Link::query()->where('short_code', $code)->doesntExist())->toBeTrue();
});

it('generates a unique code across many iterations', function () {
    config()->set('shortener.short_code.length', 7);
    config()->set('shortener.short_code.alphabet', 'abcdefghijkmnpqrstuvwxyz23456789');

    $generator = new ShortCodeGenerator;
    $codes = [];

    for ($i = 0; $i < 200; $i++) {
        $code = $generator->generate();
        expect(in_array($code, $codes, true))->toBeFalse("Duplicate generated: {$code}");
        $codes[] = $code;
    }

    expect($codes)->toHaveCount(200);
});

it('throws when unable to find a unique code within max attempts', function () {
    config()->set('shortener.short_code.length', 4);
    config()->set('shortener.short_code.alphabet', 'a');
    config()->set('shortener.short_code.max_attempts', 3);

    // Alphabet has a single character: every generated code will be 'aaaa'.
    Link::factory()->create(['short_code' => 'aaaa']);

    $generator = new ShortCodeGenerator;

    $generator->generate();
})->throws(RuntimeException::class);

it('uses the default alphabet when config is missing', function () {
    config()->set('shortener.short_code.length', 6);
    config()->set('shortener.short_code.alphabet', null);
    config()->set('shortener.short_code.max_attempts', 5);

    $generator = new ShortCodeGenerator;
    $code = $generator->generate();

    expect($code)->toMatch('/^[abcdefghijkmnpqrstuvwxyz23456789]{6}$/');
});
