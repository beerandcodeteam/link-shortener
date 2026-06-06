<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

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

        $reserved = array_map('strtolower', config('links.reserved_words', []));

        if (in_array(strtolower($value), $reserved, true)) {
            $fail('That short code is reserved. Please choose another.');
        }
    }
}
