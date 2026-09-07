<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PayrollPaymentsLivewire\Livewire\PayrollPayments;
use Livewire\Livewire;

final class PayrollPaymentsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-payroll-payments-livewire');
        Livewire::component('module-accounting-payroll-payments::batches', PayrollPayments::class);
        Livewire::component('module-accounting-payroll-payments-batches', PayrollPayments::class);
    }
}
