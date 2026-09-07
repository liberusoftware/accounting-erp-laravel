<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\WorkforceCostingLivewire\Livewire\WorkforceCosts;
use Livewire\Livewire;

final class WorkforceCostingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-workforce-costs', WorkforceCosts::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-workforce-costing');
    }
}
