<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturnsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\TaxReturnsLivewire\Livewire\TaxReturns;
use Livewire\Livewire;

final class TaxReturnsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-tax-returns-list', TaxReturns::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-tax-returns');
    }
}
