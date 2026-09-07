<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ForecastsLivewire\Livewire\Forecasts;
use Livewire\Livewire;

final class ForecastsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-forecasts::forecasts', Forecasts::class);
        Livewire::component('accounting-forecasts', Forecasts::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-forecasts-livewire');
    }
}
