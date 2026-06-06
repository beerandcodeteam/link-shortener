<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Generates URL-safe random short codes, retrying on collision.
 */
final class ShortCodeGenerator
{
    /**
     * Generate a unique short code and return it.
     */
    public static function generate(?int $length = null): string
    {
        $codeLength = $length ?? (config('link-shortener.short_code_length', 7));
        $charset = config('link-shortener.short_code_charset', 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');

        return self::generateFromCharset($codeLength, $charset);
    }

    /**
     * Generate a short code from a given charset.
     */
    public static function generateFromCharset(int $length, string $charset): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Short code length must be greater than zero.');
        }

        if ($length > 100) {
            throw new \InvalidArgumentException('Short code length must not exceed 100 characters.');
        }

        if (empty($charset)) {
            throw new \InvalidArgumentException('Charset must not be empty.');
        }

        $charsetLength = strlen($charset);
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $charset[self::randomInt(0, $charsetLength - 1)];
        }

        return $code;
    }

    /**
     * Generate a unique short code, retrying on collision.
     */
    public static function generateUnique(?int $length = null): string
    {
        $maxAttempts = 20;
        $attempts = 0;
        $defaultLength = config('link-shortener.short_code_length', 7);

        do {
            $code = self::generate($length ?? $defaultLength);
            $attempts++;

            if (! self::isCodeInUse($code)) {
                return $code;
            }
        } while ($attempts < $maxAttempts);

        // If we hit the limit, generate a much longer fallback code.
        return self::generateUnique($defaultLength + 10);
    }

    /**
     * Check if a short code already exists in the database.
     */
    public static function isCodeInUse(string $code): bool
    {
        return DB::table('links')->where('short_code', $code)->exists();
    }

    /**
     * Generate a cryptographically secure random integer within a range.
     */
    private static function randomInt(int $min, int $max): int
    {
        $range = $max - $min + 1;

        if ($range <= 0) {
            return self::randomFromZero($max);
        }

        // Generate enough bytes to cover the range.
        $bytesNeeded = (int) ceil(log(max($range, 2), 256));

        $bytes = random_bytes($bytesNeeded);
        $num = 0;

        for ($i = 0; $i < $bytesNeeded; $i++) {
            $num = ($num << 8) | ord($bytes[$i]);
        }

        return $min + ($num % $range);
    }

    private static function randomFromZero(int $max): int
    {
        if ($max < 0) {
            throw new \InvalidArgumentException('Max must be non-negative.');
        }

        $bytesNeeded = max(1, (int) ceil(log(max($max + 2, 2), 256)));

        $bytes = random_bytes($bytesNeeded);

        $threshold = -(PHP_INT_MAX + 1) % ($max + 1);

        do {
            $num = 0;
            for ($i = 0; $i < $bytesNeeded; $i++) {
                $num = ($num << 8) | ord($bytes[$i]);
            }
            if ($num >= $threshold) {
                break;
            }
            $bytes = random_bytes($bytesNeeded);
        } while (true);

        return $num % ($max + 1);
    }
}
