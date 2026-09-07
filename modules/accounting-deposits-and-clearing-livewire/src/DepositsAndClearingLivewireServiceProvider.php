<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\DepositsAndClearingLivewire\Livewire\Clearing;
use Livewire\Livewire;

final class DepositsAndClearingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-clearing');
        Livewire::component('module-accounting-deposits-and-clearing', Clearing::class);
    }
}
