<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ProductAndServiceItemsLivewire\Livewire\AccountingItems;
use Livewire\Livewire;

final class ProductAndServiceItemsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-product-and-service-items-livewire');
        Livewire::component('module-accounting-product-and-service-items::items', AccountingItems::class);
        Livewire::component('module-accounting-product-and-service-items-items', AccountingItems::class);
    }
}
