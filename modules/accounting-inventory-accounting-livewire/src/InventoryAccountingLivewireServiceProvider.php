<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\InventoryAccountingLivewire\Livewire\InventoryAccounting;
use Livewire\Livewire;

final class InventoryAccountingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-inventory-accounting::inventory', InventoryAccounting::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-inventory-accounting-livewire');
    }
}
