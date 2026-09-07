<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PayrollIntegrationLivewire\Livewire\PayrollImports;
use Livewire\Livewire;

final class PayrollIntegrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-payroll-integration-livewire');
        Livewire::component('module-accounting-payroll-integration::imports', PayrollImports::class);
        Livewire::component('module-accounting-payroll-integration-imports', PayrollImports::class);
    }
}
