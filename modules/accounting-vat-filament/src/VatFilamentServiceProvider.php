<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatFilament;

use Illuminate\Support\ServiceProvider;

final class VatFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(VatFilamentPlugin::class, fn (): VatFilamentPlugin => VatFilamentPlugin::make());
    }
}
