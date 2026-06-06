<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Validates that a short code is NOT in the reserved words list.
 */
final class ReservedShortCode implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     */
    public function passes($attribute, $value): bool
    {
        $reserved = config('link-shortener.reserved_words', []);

        return ! in_array(strtolower((string) $value), $reserved, true);
    }

    /**
     * Get the validation error message.
     *
     * @return array<string,string>
     */
    public function message(): array
    {
        return ['The :attribute is a reserved short code and cannot be used.'];
    }
}
