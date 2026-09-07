<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ProjectProfitabilityLivewire\Livewire\ProjectProfitability;
use Livewire\Livewire;

final class ProjectProfitabilityLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-project-profitability', ProjectProfitability::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-project-profitability-livewire');
    }
}
