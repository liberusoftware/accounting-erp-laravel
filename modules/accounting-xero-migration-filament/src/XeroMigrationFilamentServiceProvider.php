<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigrationFilament;

use Illuminate\Support\ServiceProvider;

final class XeroMigrationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(XeroMigrationFilamentPlugin::class, fn (): XeroMigrationFilamentPlugin => XeroMigrationFilamentPlugin::make());
    }
}
