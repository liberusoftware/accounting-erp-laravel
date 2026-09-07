<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigrationLivewire;

use Illuminate\Support\ServiceProvider;

final class SageAccountingMigrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-sage-accounting-migration');
    }
}
