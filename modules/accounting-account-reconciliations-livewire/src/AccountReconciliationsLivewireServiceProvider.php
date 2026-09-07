<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountReconciliationsLivewire\Livewire\Reconciliations;
use Livewire\Livewire;

final class AccountReconciliationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-account-reconciliations');
        Livewire::component('module-accounting-account-reconciliations::reconciliations', Reconciliations::class);
        Livewire::component('module-accounting-account-reconciliations-reconciliations', Reconciliations::class);
    }
}
