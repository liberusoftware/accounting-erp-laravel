<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\PurchaseRequisitionsLivewire\Livewire\PurchaseRequisitions;
use Livewire\Livewire;

final class PurchaseRequisitionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-purchase-requisitions');
        Livewire::component('module-accounting-purchase-requisitions::requisitions', PurchaseRequisitions::class);
        Livewire::component('module-accounting-purchase-requisitions-requisitions', PurchaseRequisitions::class);
    }
}
