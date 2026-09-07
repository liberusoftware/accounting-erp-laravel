<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\MileageLivewire\Livewire\MileageTrips;
use Livewire\Livewire;

final class MileageLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-mileage::trips', MileageTrips::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-mileage-livewire');
    }
}
