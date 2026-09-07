<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\SalesTaxAndGstLivewire\Livewire\SalesTaxRecords;
use Livewire\Livewire;

final class SalesTaxAndGstLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-sales-tax-records', SalesTaxRecords::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-sales-tax-and-gst');
    }
}
