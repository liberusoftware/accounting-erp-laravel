<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountsPayableLivewire\Livewire\Aging;
use Liberu\Accounting\AccountsPayableLivewire\Livewire\Payables;
use Liberu\Accounting\AccountsPayableLivewire\Livewire\Reconciliation;
use Livewire\Livewire;

final class AccountsPayableLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-accounts-payable');
        Livewire::component('module-accounting-accounts-payable::payables', Payables::class);
        Livewire::component('module-accounting-accounts-payable-payables', Payables::class);
        Livewire::component('module-accounting-accounts-payable::aging', Aging::class);
        Livewire::component('module-accounting-accounts-payable-aging', Aging::class);
        Livewire::component('module-accounting-accounts-payable::reconciliation', Reconciliation::class);
        Livewire::component('module-accounting-accounts-payable-reconciliation', Reconciliation::class);
    }
}
