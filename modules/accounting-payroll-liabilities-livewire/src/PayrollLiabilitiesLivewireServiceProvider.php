<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PayrollLiabilitiesLivewire\Livewire\PayrollLiabilities;
use Livewire\Livewire;

final class PayrollLiabilitiesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-payroll-liabilities-livewire');
        Livewire::component('module-accounting-payroll-liabilities::liabilities', PayrollLiabilities::class);
        Livewire::component('module-accounting-payroll-liabilities-liabilities', PayrollLiabilities::class);
    }
}
