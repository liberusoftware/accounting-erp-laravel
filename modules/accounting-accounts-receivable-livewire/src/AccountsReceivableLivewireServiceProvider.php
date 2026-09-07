<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountsReceivableLivewire\Livewire\Aging;
use Liberu\Accounting\AccountsReceivableLivewire\Livewire\Receivables;
use Liberu\Accounting\AccountsReceivableLivewire\Livewire\Statement;
use Livewire\Livewire;

class AccountsReceivableLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-accounts-receivable');
        Livewire::addNamespace(
            'module-accounting-accounts-receivable',
            __DIR__.'/../resources/views/livewire',
            'Liberu\\Accounting\\AccountsReceivableLivewire\\Livewire',
            __DIR__.'/Livewire',
            __DIR__.'/../resources/views/livewire',
        );
        Livewire::component('module-accounting-accounts-receivable::receivables', Receivables::class);
        Livewire::component('module-accounting-accounts-receivable::aging', Aging::class);
        Livewire::component('module-accounting-accounts-receivable::statement', Statement::class);
        Livewire::component('module-accounting-accounts-receivable-receivables', Receivables::class);
        Livewire::component('module-accounting-accounts-receivable-aging', Aging::class);
        Livewire::component('module-accounting-accounts-receivable-statement', Statement::class);
        Livewire::component('accounting-accounts-receivables', Receivables::class);
    }
}
