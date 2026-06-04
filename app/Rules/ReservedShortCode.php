<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Reject a short code that collides with a reserved word (route name,
 * sensitive endpoint, or commonly confused term). Comparison is
 * case-insensitive.
 */
class ReservedShortCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $reserved = $this->reservedWords();

        if (in_array(strtolower($value), $reserved, true)) {
            $fail('The :attribute is reserved and cannot be used.')->translate();
        }
    }

    /**
     * The reserved words list, merged with anything configured by the
     * application.
     *
     * @return array<int, string>
     */
    protected function reservedWords(): array
    {
        $configured = (array) config('shortener.reserved', []);

        return array_map('strtolower', $configured);
    }
}
