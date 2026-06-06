<?php

declare(strict_types=1);

use App\Rules\ReservedShortCode;

$rule = new ReservedShortCode();

it('passes for normal short codes', function () use ($rule): void {
    expect($rule->passes('short_code', 'abc'))->toBeTrue();
    expect($rule->passes('short_code', 'xyz123'))->toBeTrue();
    expect($rule->passes('short_code', 'HelloWorld'))->toBeTrue();
    expect($rule->passes('short_code', 'randomcode99'))->toBeTrue();
});

it('fails for each reserved word', function (string $word) use ($rule): void {
    expect($rule->passes('short_code', $word))->toBeFalse();
})->with([
    'login' => ['login'],
    'register' => ['register'],
    'logout' => ['logout'],
    'dashboard' => ['dashboard'],
    'links' => ['links'],
    'settings' => ['settings'],
    'profile' => ['profile'],
    'help' => ['help'],
    'docs' => ['docs'],
    'api' => ['api'],
]);

it('is case-insensitive', function () use ($rule): void {
    expect($rule->passes('short_code', 'LOGIN'))->toBeFalse();
    expect($rule->passes('short_code', 'Login'))->toBeFalse();
    expect($rule->passes('short_code', 'rEgIsTeR'))->toBeFalse();
    expect($rule->passes('short_code', 'DaShBoArD'))->toBeFalse();
});
