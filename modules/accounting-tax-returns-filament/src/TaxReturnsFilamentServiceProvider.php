<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsFilament;

use Illuminate\Support\ServiceProvider;

final class TaxReturnsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TaxReturnsFilamentPlugin::class, fn (): TaxReturnsFilamentPlugin => TaxReturnsFilamentPlugin::make());
    }
}
