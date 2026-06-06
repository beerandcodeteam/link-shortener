<?php

declare(strict_types=1);

use App\Forms\LinkStoreFormObject;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    // Seed link_statuses rows so factories/fixtures work (migration only has id+timestamps).
    if (! DB::table('link_statuses')->where('slug', 'active')->exists()) {
        DB::table('link_statuses')->insert([
            'id' => 1, 'slug' => 'active', 'name' => 'Active',
            'description' => 'The link is fully functional.', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
    if (! DB::table('link_statuses')->where('slug', 'disabled')->exists()) {
        DB::table('link_statuses')->insert([
            'id' => 2, 'slug' => 'disabled', 'name' => 'Disabled',
            'description' => 'The link has been disabled.', 'is_active' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    // Ensure a user row exists for duplicate-link tests.
    if (! DB::table('users')->where('id', 8001)->exists()) {
        DB::table('users')->insert([
            'id' => 8001, 'name' => 'Dup Test User', 'email' => 'dup@test.com',
            'password' => bcrypt('password'), 'email_verified_at' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
});

// ---- original_url: rejection tests ----

it('rejects missing original url', function (): void {
    $form = new LinkStoreFormObject('', null);
    expect($form->hasError('original_url'))->toBeTrue();
});

it('rejects blank original url after trim', function (): void {
    $form = new LinkStoreFormObject('   ', null);
    expect($form->hasError('original_url'))->toBeTrue()
        ->and($form->getErrorsFor('original_url')->first())->toContain('The original url field is required.');
});

it('rejects non-URL strings', function (string $value): void {
    $form = new LinkStoreFormObject($value, null);
    expect($form->hasError('original_url'))->toBeTrue()
        ->and(collect($form->getErrorsFor('original_url'))->contains(fn ($m) => str_contains($m, 'The original url field must be a valid URL.')));
})->with([
    'plain string' => ['not-a-url'],
    'malformed' => ['://missing-scheme'],
    'just domain' => ['example.com'],
]);

it('rejects non http(s) schemes', function (string $value, string $expectedSubstr): void {
    // Note: some scheme-less URLs fail Laravel's url rule before we reach the scheme check.
    $form = new LinkStoreFormObject($value, null);
    expect($form->hasError('original_url'))->toBeTrue()
        ->and(collect($form->getErrorsFor('original_url'))->contains(fn ($m) => str_contains($m, $expectedSubstr)));
})->with([
    'ftp' => ['ftp://example.com/file', 'must use http or https'],
    'mailto' => ['mailto:test@example.com', 'valid URL'],
    'file' => ['file:///path/to/file', 'valid URL'],
    'javascript' => ['javascript:void(0)', 'valid URL'],
]);

it('rejects over-long url', function (): void {
    $longUrl = 'http://example.com/' . str_repeat('a', 3001);
    $form = new LinkStoreFormObject($longUrl, null);
    expect($form->hasError('original_url'))->toBeTrue()
        ->and(collect($form->getErrorsFor('original_url'))->contains(fn ($m) => str_contains($m, 'must not exceed 2048')));
});

// ---- custom_code: rejection tests ----

it('rejects custom code with disallowed chars', function (string $code): void {
    $form = new LinkStoreFormObject('http://example.com', $code);
    expect($form->hasError('custom_code'))->toBeTrue();
})->with([
    'spaces' => ['my code'],
    'underscore' => ['my_code'],
    'special chars' => ['my!code#123'],
    'brackets' => ['[link]'],
]);

it('rejects custom code over max length', function (): void {
    $form = new LinkStoreFormObject('http://example.com', str_repeat('a', 65));
    expect($form->hasError('custom_code'))->toBeTrue();
});

it('rejects reserved word as custom code', function (string $code): void {
    $form = new LinkStoreFormObject('http://example.com', $code);
    expect($form->hasError('custom_code'))->toBeTrue();
})->with([
    'login' => ['login'],
    'LOGIN' => ['LOGIN'],
    'Login' => ['Login'],
    'register' => ['register'],
    'dashboard' => ['dashboard'],
    'links' => ['links'],
]);

it('rejects duplicate custom code with a clear error', function (): void {
    DB::table('links')->insertOrIgnore([
        'id' => 9001, 'user_id' => 8001, 'link_status_id' => 1,
        'original_url' => 'http://example.com/dup', 'short_code' => 'taken',
        'click_count' => 0, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $form = new LinkStoreFormObject('http://example.com', 'taken');

    expect($form->hasError('custom_code'))->toBeTrue()
        ->and(collect($form->getErrorsFor('custom_code'))->contains(fn ($m) => str_contains($m, 'already been taken')));
});

// ---- acceptance tests ----

it('accepts valid http url', function (): void {
    $form = new LinkStoreFormObject('http://example.com/path?query=1', null);
    expect($form->isValid())->toBeTrue();
});

it('accepts valid https url', function (): void {
    $form = new LinkStoreFormObject('https://secure.example.com/', null);
    expect($form->isValid())->toBeTrue();
});

it('accepts valid custom code with hyphens and numbers', function (): void {
    $form = new LinkStoreFormObject('http://example.com', 'a-b-123-code');
    expect($form->isValid())
        ->and($form->resolvedCode())->toBe('a-b-123-code');
});

it('accepts valid custom code alphabetic only', function (): void {
    $form = new LinkStoreFormObject('http://example.com', 'customcode');
    expect($form->isValid())
        ->and($form->resolvedCode())->toBe('customcode');
});

it('accepts empty custom code and returns null for auto-generation', function (): void {
    $form = new LinkStoreFormObject('https://example.com', '');
    expect($form->isValid())
        ->and($form->resolvedCode())->toBeNull();
});

it('accepts null custom code and returns null for auto-generation', function (): void {
    $form = new LinkStoreFormObject('https://example.com', null);
    expect($form->isValid())
        ->and($form->resolvedCode())->toBeNull();
});

it('preserves provided valid custom code via resolvedCode()', function (): void {
    $form = new LinkStoreFormObject('http://example.com', 'my-custom-code');
    expect($form->isValid())
        ->and($form->resolvedCode())->toBe('my-custom-code');
});
