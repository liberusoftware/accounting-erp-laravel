<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankReconciliationLivewire\Livewire\Sessions;
use Livewire\Livewire;

final class BankReconciliationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-bank-reconciliation');
        Livewire::component('module-accounting-bank-reconciliation::sessions', Sessions::class);
        Livewire::component('module-accounting-bank-reconciliation-sessions', Sessions::class);
    }
}
