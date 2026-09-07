<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalFilament;

use Illuminate\Support\ServiceProvider;

final class SupplierPortalFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SupplierPortalFilamentPlugin::class);
    }
}
