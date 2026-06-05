<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->registerRateLimiters();
    }

    /**
     * Register the named rate limiters used by the public surface.
     *
     * The `shorten` limiter caps submissions on the public homepage
     * "Shorten" form. The `shorten-redirect` limiter caps visits to
     * resolved short codes. Both buckets are keyed by the originating
     * IP so a single noisy client cannot exhaust the public quota.
     */
    protected function registerRateLimiters(): void
    {
        RateLimiter::for('shorten', function (Request $request): Limit {
            $max = (int) config('shortener.rate_limit.shorten_per_minute', 10);

            return Limit::perMinute($max)->by((string) $request->ip());
        });

        RateLimiter::for('shorten-redirect', function (Request $request): Limit {
            $max = (int) config('shortener.rate_limit.redirect_per_minute', 60);

            return Limit::perMinute($max)->by((string) $request->ip());
        });
    }
}
