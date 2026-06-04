<?php

namespace App\Services;

use App\Models\Link;

/**
 * Generates URL-safe short codes for links.
 *
 * Codes use a configurable alphabet (default excludes visually ambiguous
 * characters such as 0/O, 1/l/I) and length. When a generated code
 * already exists on the `links` table, the service retries until it
 * produces a unique value, bounded by `shortener.short_code.max_attempts`.
 */
class ShortCodeGenerator
{
    /**
     * Default alphabet: URL-safe, no ambiguous characters.
     */
    public const DEFAULT_ALPHABET = 'abcdefghijkmnpqrstuvwxyz23456789';

    /**
     * Generate a unique short code.
     */
    public function generate(?int $length = null): string
    {
        $length = $length ?? (int) config('shortener.short_code.length', 7);
        $alphabet = (string) config('shortener.short_code.alphabet', self::DEFAULT_ALPHABET);
        $maxAttempts = (int) config('shortener.short_code.max_attempts', 10);

        return $this->generateWith($length, $alphabet, $maxAttempts);
    }

    /**
     * Generate a unique short code using the given alphabet.
     */
    public function generateWith(int $length, string $alphabet, int $maxAttempts = 10): string
    {
        $alphabet = $this->normalizeAlphabet($alphabet);

        if ($length < 1) {
            throw new \InvalidArgumentException('Short code length must be at least 1.');
        }

        if ($alphabet === '') {
            throw new \InvalidArgumentException('Short code alphabet must contain at least one character.');
        }

        if ($maxAttempts < 1) {
            throw new \InvalidArgumentException('Max attempts must be at least 1.');
        }

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = $this->randomCode($length, $alphabet);

            if (! $this->exists($code)) {
                return $code;
            }
        }

        throw new \RuntimeException(
            "Unable to generate a unique short code after {$maxAttempts} attempts."
        );
    }

    /**
     * Determine if the given code already exists in the links table.
     */
    public function exists(string $code): bool
    {
        return Link::query()->where('short_code', $code)->exists();
    }

    /**
     * Build a random code of the given length from the given alphabet.
     */
    protected function randomCode(int $length, string $alphabet): string
    {
        $alphabetMax = strlen($alphabet) - 1;

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $alphabetMax)];
        }

        return $code;
    }

    /**
     * Validate and clean the supplied alphabet.
     */
    protected function normalizeAlphabet(string $alphabet): string
    {
        $cleaned = preg_replace('/\s+/u', '', $alphabet) ?? '';

        if ($cleaned === '' || $cleaned === null) {
            return self::DEFAULT_ALPHABET;
        }

        return $cleaned;
    }
}
