<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoansLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\DebtAndLoansLivewire\Livewire\DebtPosition;
use Livewire\Livewire;

final class DebtAndLoansLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-debt-and-loans');
        Livewire::component('module-accounting-debt-and-loans', DebtPosition::class);
    }
}
