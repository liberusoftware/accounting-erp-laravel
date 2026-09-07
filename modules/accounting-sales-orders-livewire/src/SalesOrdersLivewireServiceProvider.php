<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrdersLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\SalesOrdersLivewire\Livewire\SalesOrders;
use Livewire\Livewire;

final class SalesOrdersLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-sales-orders');
        Livewire::component('module-accounting-sales-orders::orders', SalesOrders::class);
        Livewire::component('module-accounting-sales-orders-orders', SalesOrders::class);
    }
}
