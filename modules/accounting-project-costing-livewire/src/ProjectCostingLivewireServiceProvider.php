<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCostingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ProjectCostingLivewire\Livewire\ProjectCosts;
use Livewire\Livewire;

final class ProjectCostingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-project-costs', ProjectCosts::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-project-costing');
    }
}
