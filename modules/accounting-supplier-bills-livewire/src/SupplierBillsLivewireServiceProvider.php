<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\SupplierBillsLivewire\Livewire\SupplierBills;
use Livewire\Livewire;

final class SupplierBillsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-supplier-bills');
        Livewire::addNamespace(
            'module-accounting-supplier-bills',
            __DIR__.'/../resources/views/livewire',
            'Liberu\\Accounting\\SupplierBillsLivewire\\Livewire',
            __DIR__.'/Livewire',
            __DIR__.'/../resources/views/livewire',
        );
        Livewire::component('module-accounting-supplier-bills::supplier-bills', SupplierBills::class);
        Livewire::component('module-accounting-supplier-bills-supplier-bills', SupplierBills::class);
    }
}
