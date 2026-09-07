<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CarbonAccountingLivewire\Livewire\CarbonActivities;
use Livewire\Livewire;

final class CarbonAccountingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-carbon-accounting');
        Livewire::component('accounting-carbon-accounting', CarbonActivities::class);
    }
}
