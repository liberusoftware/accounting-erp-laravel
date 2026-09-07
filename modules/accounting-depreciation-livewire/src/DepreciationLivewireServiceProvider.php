<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepreciationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\DepreciationLivewire\Livewire\DepreciationSchedules;
use Livewire\Livewire;

final class DepreciationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-depreciation');
        Livewire::component('module-accounting-depreciation-schedules', DepreciationSchedules::class);
    }
}
