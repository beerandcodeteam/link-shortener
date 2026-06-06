<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Short Code Length
    |--------------------------------------------------------------------------
    |
    | The length of the generated short codes. A longer code reduces collision
    | probability but produces longer URLs. Default is 7 characters.
    |
    */

    'short_code_length' => (int) env('SHORT_CODE_LENGTH', 7),

    /*
    |--------------------------------------------------------------------------
    | Short Code Charset
    |--------------------------------------------------------------------------
    |
    | The character set used for generating short codes. We exclude ambiguous
    | characters that can be confused visually (e.g., O/o, I/l/1, B/b/8).
    |
    */

    'short_code_charset' => env('SHORT_CODE_CHARSET', 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'),

    /*
    |--------------------------------------------------------------------------
    | Reserved Short Codes
    |--------------------------------------------------------------------------
    |
    | List of short codes that must never be generated. These typically
    | map to application routes (login, register, dashboard, etc.) so they
    | are not overridden by user-created links.
    |
    */

    'reserved_words' => array_merge(
        env('RESERVED_WORDS', '') !== ''
            ? array_filter(explode(',', env('RESERVED_WORDS')))
            : [],
        [
            'login',
            'register',
            'logout',
            'dashboard',
            'links',
            'settings',
            'profile',
            'help',
            'docs',
            'api',
        ],
    ),

];
