<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BudgetsLivewire\Livewire\Budgets;
use Livewire\Livewire;

final class BudgetsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-budgets');
        Livewire::component('accounting-budgets', Budgets::class);
    }
}
