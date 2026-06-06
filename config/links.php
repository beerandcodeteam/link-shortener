<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto-Generated Short Code
    |--------------------------------------------------------------------------
    |
    | Settings used by the ShortCodeGenerator service. The alphabet excludes
    | visually ambiguous characters (0/O, 1/l/I) so generated codes stay
    | URL-safe and easy to read aloud.
    |
    */

    'short_code' => [
        'length' => 7,
        'alphabet' => '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
        'max_collision_attempts' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Short Code
    |--------------------------------------------------------------------------
    |
    | Constraints applied to user-supplied custom codes. The pattern allows
    | letters, digits, hyphens and underscores only.
    |
    */

    'custom_code' => [
        'min_length' => 3,
        'max_length' => 30,
        'pattern' => '/^[A-Za-z0-9_-]+$/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Original URL
    |--------------------------------------------------------------------------
    */

    'original_url' => [
        'max_length' => 2048,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reserved Words
    |--------------------------------------------------------------------------
    |
    | Short codes that would collide with application routes or are otherwise
    | not allowed. Matched case-insensitively by the ReservedShortCode rule.
    |
    */

    'reserved_words' => [
        'login',
        'register',
        'logout',
        'dashboard',
        'links',
        'link',
        'admin',
        'api',
        'password',
        'profile',
        'settings',
        'home',
        'about',
        'terms',
        'privacy',
        'analytics',
    ],

];
