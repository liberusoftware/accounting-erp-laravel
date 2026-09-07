<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\SupplierPortalLivewire\Livewire\PortalResources;
use Livewire\Livewire;

final class SupplierPortalLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-supplier-portal-resources', PortalResources::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-supplier-portal');
    }
}
