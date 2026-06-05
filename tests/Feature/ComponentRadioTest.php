<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;

/**
 * NOTE: Since these are Blade components and not Livewire/Controller-driven routes necessarily,
 * we test the rendered HTML from a small blade snippet to ensure parameters (label, options, selected)
 * are correctly reflected in the output.
 */

test('radio component renders labels', function () {
    $html = Blade::render('<x-radio label="Check Me" />');
    expect($html)->toContain('Check Me');
});

test('radio group renders items and state', function () {
    // Mocking a scenario where radio_group is used with options
    $options = [
        'val1' => 'Option 1',
        'val2' => 'Option 2',
    ];

    $html = Blade::render('<x-radio-group label="Choose" :options="$options" selected="val1" />');

    expect($html)->toContain('Choose')
                  ->toContain('Option 1')
                  ->toContain('Option 2')
                  ->toContain('checked'); // Since val1 is checked
});

test('radio group shows error state', function () {
    $html = Blade::render('<x-radio-group label="Required" :error="\'Field is required\"" />');
    expect($html)->toContain('Field is required')
                  ->toContain('text-red'); // Ensuring the CSS class from our implementation is present
});
