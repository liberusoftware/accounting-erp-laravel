<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PurchaseOrdersLivewire\Livewire\PurchaseOrders;
use Livewire\Livewire;

final class PurchaseOrdersLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-purchase-orders');
        Livewire::component('module-accounting-purchase-orders::orders', PurchaseOrders::class);
        Livewire::component('module-accounting-purchase-orders-orders', PurchaseOrders::class);
    }
}
