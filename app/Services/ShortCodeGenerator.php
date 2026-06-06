<?php

namespace App\Services;

use App\Models\Link;
use RuntimeException;

class ShortCodeGenerator
{
    /**
     * Generate a unique, URL-safe short code, retrying on collisions.
     *
     * @throws RuntimeException When a unique code cannot be produced within the configured attempts.
     */
    public function generate(?int $length = null): string
    {
        $length ??= (int) config('links.short_code.length');
        $alphabet = (string) config('links.short_code.alphabet');
        $maxAttempts = (int) config('links.short_code.max_collision_attempts');

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = $this->randomCode($length, $alphabet);

            if (! Link::where('short_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException("Unable to generate a unique short code after {$maxAttempts} attempts.");
    }

    /**
     * Build a random code of the given length from the allowed alphabet.
     */
    private function randomCode(int $length, string $alphabet): string
    {
        $maxIndex = strlen($alphabet) - 1;
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $maxIndex)];
        }

        return $code;
    }
}
