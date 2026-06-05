<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Rule to ensure the short code is not a reserved word.
 */
class ReservedShortCode implements ValidationRule
{
    /**
     * The list of terms that are forbidden as custom short codes.
     *
     * @var array<string>
     */
    protected array $reservedWords = [
        'login',
        'register',
        'dashboard',
        'links',
        'admin',
        'account',
        'api',
        'profile',
        'home',
    ];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $normalized = strtolower(trim((string)$value));

        if (in_array($normalized, $this->reservedWords, true)) {
            $fail("The {$attribute} cannot be a reserved word (e.g., login, dashboard).");
        }
    }
}
