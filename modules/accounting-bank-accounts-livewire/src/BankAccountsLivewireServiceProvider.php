<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankAccountsLivewire\Livewire\Accounts;
use Livewire\Livewire;

final class BankAccountsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-bank-accounts');
        Livewire::component('module-accounting-bank-accounts::accounts', Accounts::class);
        Livewire::component('module-accounting-bank-accounts-accounts', Accounts::class);
    }
}
