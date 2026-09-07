<?php

declare(strict_types=1);

namespace Liberu\Accounting\DashboardsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\DashboardsLivewire\Livewire\DashboardOverview;
use Livewire\Livewire;

final class DashboardsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-dashboards');
        Livewire::component('module-accounting-dashboards', DashboardOverview::class);
    }
}
