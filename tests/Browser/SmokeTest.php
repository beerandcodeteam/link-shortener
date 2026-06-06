<?php

use App\Models\Link;
use App\Models\User;
use Composer\InstalledVersions;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

/**
 * Whether the optional browser-testing plugin is installed in this environment.
 */
function browserPluginInstalled(): bool
{
    return InstalledVersions::isInstalled('pestphp/pest-plugin-browser');
}

/*
|--------------------------------------------------------------------------
| Browser smoke tests (Pest 4)
|--------------------------------------------------------------------------
|
| These visit the application's key pages in a real browser and assert there
| are no JavaScript errors or console logs (assertNoSmoke). They require the
| pestphp/pest-plugin-browser dev dependency; when it is absent the global
| visit() helper is undefined, so the suite skips them instead of erroring.
*/

it('renders the public and auth pages without JavaScript errors', function () {
    visit(['/', '/login', '/register', '/forgot-password'])
        ->assertNoSmoke();
})->skip(! browserPluginInstalled(), 'pest-plugin-browser is not installed.');

it('renders the dashboard and link detail without JavaScript errors', function () {
    $user = User::factory()->create();
    $link = Link::factory()->active()->forUser($user)->create();

    $this->actingAs($user);

    visit(['/dashboard', '/links/'.$link->id])
        ->assertNoSmoke();
})->skip(! browserPluginInstalled(), 'pest-plugin-browser is not installed.');
