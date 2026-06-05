<?php

use App\Rules\ReservedShortCode;
use Illuminate\Support\Facades\Validator;

/**
 * Rule test for ReservedShortCode.
 */

test('it allows non-reserved words', function () {
    $rule = new ReservedShortCode();

    // Should not fail
    $rule->validate('short_code', 'myUniqueLink30x', fn() => null); // No exception or error should be triggered

    $validator = Validator::make(
        ['short_code' => 'myUniqueLink30x'],
        ['short_code' => ['required', new ReservedShortCode()]]
    );

    expect($validator->passes())->toBeTrue();
});

test('it rejects reserved words', function () {
    $reservedWords = ['login', 'dashboard', 'register', 'links', 'admin', 'account', 'api', 'profile', 'home'];

    foreach ($reservedWords as $word) {
        $validator = Validator::make(
            ['short_code' => $word],
            ['short_code' => ['required', new ReservedShortCode()]]
        );

        // The validator fails because the rule triggers the fail closure
        expect($validator->passes())->toBeFalse();
    }
});

test('it allows empty values (handled by other rules)', function () {
    $rule = new ReservedShortCode();

    // Should not fail for an empty value as it's technically not a reserved word
    $rule->validate('short_code', '', fn() => null);

    $validator = Validator::make(
        ['short_code' => ''],
        ['short_code' => ['nullable', new ReservedShortCode()]]
    );

    expect($validator->passes())->toBeTrue();
});
