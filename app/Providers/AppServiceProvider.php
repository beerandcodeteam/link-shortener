<?php

namespace App\Providers;

use App\Models\Link;
use App\Policies\LinkPolicy;
use http\Client\Curl\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        auth()->login(\App\Models\User::first());
        Gate::policy(Link::class, LinkPolicy::class);

    }
}
