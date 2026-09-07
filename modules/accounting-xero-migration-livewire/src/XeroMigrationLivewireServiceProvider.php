<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\XeroMigrationLivewire\Livewire\XeroConnections;
use Livewire\Livewire;

final class XeroMigrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-xero-migration-connections', XeroConnections::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-xero-migration');
    }
}
