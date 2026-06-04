<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Short Code Generation
    |--------------------------------------------------------------------------
    |
    | Configuration for the ShortCodeGenerator service. The alphabet is a
    | URL-safe set with ambiguous characters removed (no 0/O, 1/l/I) so
    | codes are easier to share verbally and copy/paste reliably.
    |
    */

    'short_code' => [
        'length' => (int) env('SHORT_CODE_LENGTH', 7),
        'alphabet' => env('SHORT_CODE_ALPHABET', 'abcdefghijkmnpqrstuvwxyz23456789'),
        'max_attempts' => (int) env('SHORT_CODE_MAX_ATTEMPTS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Short Code Constraints
    |--------------------------------------------------------------------------
    |
    | Bounds for user-supplied custom codes. The `alphabet` mirrors the
    | generator's set by default but can be tightened to alphanumeric
    | only if desired.
    |
    */

    'custom_code' => [
        'min_length' => (int) env('CUSTOM_CODE_MIN_LENGTH', 3),
        'max_length' => (int) env('CUSTOM_CODE_MAX_LENGTH', 32),
        'alphabet' => env('CUSTOM_CODE_ALPHABET', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Validation
    |--------------------------------------------------------------------------
    |
    | `max_length` caps the original URL we accept. The default (2048) matches
    | the de-facto limit imposed by most browsers and servers.
    |
    */

    'url' => [
        'max_length' => (int) env('LINK_URL_MAX_LENGTH', 2048),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reserved Short Codes
    |--------------------------------------------------------------------------
    |
    | Codes that can never be claimed by a link because they collide with
    | application routes, sensitive endpoints, or commonly-confused words.
    | Comparison is case-insensitive.
    |
    */

    'reserved' => [
        'login',
        'register',
        'logout',
        'dashboard',
        'links',
        'admin',
        'api',
        'password',
        'forgot-password',
        'reset-password',
        'verify-email',
        'profile',
        'settings',
        'home',
        'about',
        'help',
        'terms',
        'privacy',
        'r',
    ],
];
