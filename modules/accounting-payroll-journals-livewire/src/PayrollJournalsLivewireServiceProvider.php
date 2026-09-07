<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PayrollJournalsLivewire\Livewire\PayrollJournals;
use Livewire\Livewire;

final class PayrollJournalsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-payroll-journals-livewire');
        Livewire::component('module-accounting-payroll-journals::journals', PayrollJournals::class);
        Livewire::component('module-accounting-payroll-journals-journals', PayrollJournals::class);
    }
}
