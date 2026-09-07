<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\LeasesLivewire\Livewire\Leases;
use Livewire\Livewire;

final class LeasesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-leases::leases', Leases::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-leases-livewire');
    }
}
