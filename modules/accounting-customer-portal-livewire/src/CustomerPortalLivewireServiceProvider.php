<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortalLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CustomerPortalLivewire\Livewire\PortalOverview;
use Livewire\Livewire;

final class CustomerPortalLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-customer-portal');
        Livewire::component('module-accounting-customer-portal', PortalOverview::class);
    }
}
