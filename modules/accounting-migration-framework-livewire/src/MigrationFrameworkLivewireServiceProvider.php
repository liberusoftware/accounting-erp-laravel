<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\MigrationFrameworkLivewire\Livewire\MigrationBatches;
use Livewire\Livewire;

final class MigrationFrameworkLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-migration-framework::batches', MigrationBatches::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-migration-framework-livewire');
    }
}
