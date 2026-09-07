<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatementsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\FinancialStatementsLivewire\Livewire\Statements;
use Livewire\Livewire;

final class FinancialStatementsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-financial-statements-livewire');
        Livewire::component('module-accounting-financial-statements::statements', Statements::class);
        Livewire::component('financial-statements', Statements::class);
    }
}
