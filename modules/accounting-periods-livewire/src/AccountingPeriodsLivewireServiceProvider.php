<?php

namespace Liberu\Accounting\PeriodsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PeriodsLivewire\Livewire\Periods;
use Livewire\Livewire;

final class AccountingPeriodsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-periods-livewire');
        Livewire::component('module-accounting-periods-periods', Periods::class);
        Livewire::component('module-accounting-periods::periods', Periods::class);
    }
}
