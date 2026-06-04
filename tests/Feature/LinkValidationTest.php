<?php

use App\Forms\LinkForm;
use App\Models\Link;
use Database\Seeders\LookupSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

/**
 * Build a validator using the Form Object's rules. Mirrors how Laravel
 * resolves validation when the FormRequest is dispatched, so we exercise
 * the same rule pipeline without spinning up a controller.
 */
function validateLink(array $data): array
{
    $form = new LinkForm;
    $validator = Validator::make($data, $form->rules(), $form->messages());

    return [
        'passes' => $validator->passes(),
        'errors' => $validator->errors()->toArray(),
    ];
}

it('rejects a non-URL value', function () {
    $result = validateLink(['original_url' => 'not a url']);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('original_url');
});

it('rejects a URL with a non-http(s) scheme', function () {
    $result = validateLink(['original_url' => 'ftp://example.com/file.zip']);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('original_url');
});

it('rejects an over-long URL', function () {
    $long = 'https://example.com/'.str_repeat('a', 3000);

    $result = validateLink(['original_url' => $long]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('original_url');
});

it('requires the original_url field', function () {
    $result = validateLink([]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('original_url');
});

it('accepts a valid http URL', function () {
    $result = validateLink(['original_url' => 'http://example.com/page']);

    expect($result['passes'])->toBeTrue()
        ->and($result['errors'])->toBe([]);
});

it('accepts a valid https URL', function () {
    $result = validateLink(['original_url' => 'https://example.com/some/path?x=1']);

    expect($result['passes'])->toBeTrue();
});

it('accepts a valid custom code', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'my-link_42',
    ]);

    expect($result['passes'])->toBeTrue()
        ->and($result['errors'])->toBe([]);
});

it('rejects a custom code with disallowed characters', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'has spaces!',
    ]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('custom_code');
});

it('rejects a custom code that is too short', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'ab',
    ]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('custom_code');
});

it('rejects a custom code that is too long', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => str_repeat('a', 50),
    ]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('custom_code');
});

it('rejects a duplicate custom code with a clear error', function () {
    Link::factory()->create(['short_code' => 'taken12']);

    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'taken12',
    ]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('custom_code');

    $message = $result['errors']['custom_code'][0];
    expect($message)->toContain('already taken');
});

it('rejects a reserved-word custom code', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'login',
    ]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('custom_code');
});

it('rejects a reserved-word custom code regardless of case', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => 'Dashboard',
    ]);

    expect($result['passes'])->toBeFalse()
        ->and($result['errors'])->toHaveKey('custom_code');
});

it('accepts an empty custom code so the auto-generate path can take over', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
        'custom_code' => '',
    ]);

    expect($result['passes'])->toBeTrue();
});

it('accepts a missing custom_code key for the auto-generate path', function () {
    $result = validateLink([
        'original_url' => 'https://example.com',
    ]);

    expect($result['passes'])->toBeTrue();
});

it('resolves a custom code when present and auto-generates otherwise', function () {
    $customForm = LinkForm::createFrom(
        Request::create('/', 'POST', [
            'original_url' => 'https://example.com',
            'custom_code' => 'pickme123',
        ])
    );
    $customForm->setContainer(app())->setRedirector(app('redirect'));
    $customForm->validateResolved();

    $resolvedCustom = $customForm->resolved();
    expect($resolvedCustom['original_url'])->toBe('https://example.com');
    expect($resolvedCustom['short_code'])->toBe('pickme123');

    $autoForm = LinkForm::createFrom(
        Request::create('/', 'POST', [
            'original_url' => 'https://example.com',
        ])
    );
    $autoForm->setContainer(app())->setRedirector(app('redirect'));
    $autoForm->validateResolved();

    $resolvedAuto = $autoForm->resolved();
    expect($resolvedAuto['short_code'])->toMatch('/^[abcdefghijkmnpqrstuvwxyz23456789]+$/');
    expect(Link::query()->where('short_code', $resolvedAuto['short_code'])->doesntExist())->toBeTrue();
});
