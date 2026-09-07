<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\KpiAndGoalsLivewire\Livewire\Goals;
use Livewire\Livewire;

final class KpiAndGoalsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-kpi-and-goals::goals', Goals::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-kpi-and-goals-livewire');
    }
}
