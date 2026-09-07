<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BusinessInsightsLivewire\Livewire\Insights;
use Livewire\Livewire;

final class BusinessInsightsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-business-insights');
        Livewire::component('accounting-business-insights', Insights::class);
    }
}
