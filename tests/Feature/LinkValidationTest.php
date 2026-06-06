<?php

use App\Livewire\Forms\LinkForm;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

/**
 * Validate the given data against the canonical link creation rules.
 *
 * @param  array<string, mixed>  $data
 */
function validateLink(array $data): Illuminate\Validation\Validator
{
    return Validator::make($data, LinkForm::validationRules(), LinkForm::validationMessages());
}

it('rejects a value that is not a URL', function () {
    $validator = validateLink(['original_url' => 'not a url']);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('original_url'))->toBeTrue();
});

it('rejects a non-http(s) scheme', function () {
    $validator = validateLink(['original_url' => 'ftp://example.com/file.zip']);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('original_url'))->toBeTrue();
});

it('rejects an over-long URL', function () {
    $max = (int) config('links.original_url.max_length');
    $longUrl = 'https://example.com/'.str_repeat('a', $max);

    $validator = validateLink(['original_url' => $longUrl]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('original_url'))->toBeTrue();
});

it('accepts a valid URL with a valid custom code', function () {
    $validator = validateLink([
        'original_url' => 'https://example.com/some/path',
        'custom_code' => 'my-link_1',
    ]);

    expect($validator->fails())->toBeFalse();
});

it('rejects a duplicate custom code with a clear error', function () {
    Link::factory()->create(['short_code' => 'taken1']);

    $validator = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'taken1',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('custom_code'))
        ->toBe('That short code is already taken. Please choose another.');
});

it('rejects a reserved word as a custom code', function () {
    $validator = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'dashboard',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('custom_code'))
        ->toBe('That short code is reserved. Please choose another.');
});

it('rejects a custom code with disallowed characters', function () {
    $validator = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'bad code!',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('custom_code'))->toBeTrue();
});

it('accepts an empty custom code for the auto-generate path', function () {
    $validator = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => '',
    ]);

    expect($validator->fails())->toBeFalse();
});
