<?php

use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the styled 404 view for an unknown short code', function () {
    $response = $this->get('/missing999');

    $response->assertStatus(404);
    $response->assertSee("We couldn't find that page", false);
    $response->assertSee('Go to homepage', false);
});

it('renders the styled 404 view for an unknown application path', function () {
    $response = $this->get('/this/path/does/not/exist');

    $response->assertStatus(404);
    $response->assertSee("We couldn't find that page", false);
});
