<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\GeneralLedgerLivewire\Livewire\Journals;
use Livewire\Livewire;

final class GeneralLedgerLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-general-ledger::journals', Journals::class);
        Livewire::component('accounting-general-ledger-journals', Journals::class);
    }
}
