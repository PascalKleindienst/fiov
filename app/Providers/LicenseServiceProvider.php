<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

final class LicenseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Http::macro('lemonsqueezy', fn (string $url = '') => Http::baseUrl('https://api.lemonsqueezy.com/v1/'.$url));
        Http::macro('license', fn () => Http::lemonsqueezy('licenses'));
    }
}
