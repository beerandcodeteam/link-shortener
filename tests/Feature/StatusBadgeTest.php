<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

/**
 * @test
 * tests the StatusBadge component for different states.
 */
it('renders a status badge correctly based on the status', function () {
    // Test Active state
    $activeHtml = view('components.status-badge', ['status' => 'active'])->render();
    expect($activeHtml)->toContain('Active');
    expect($activeHtml)->toContain('bg-green-tint');
    expect($activeHtml)->toContain('bg-green');

    // Test Disabled state
    $disabledHtml = view('components.status-badge', ['status' => 'disabled'])->render();
    expect($disabledHtml)->toContain('Disabled');
    expect($disabledHtml)->toContain('bg-gray-100');
    expect($disabledHtml)->toContain('bg-gray-400');
});

/**
 * @test
 * tests that the component respects attributes (e.g., custom classes).
 */
it('applies additional attributes to the status badge', function () {
    $html = view('components.status-badge', [
        'status' => 'active',
        'class' => 'custom-class'
    ])->render();

    expect($html)->toContain('custom-class');
});
