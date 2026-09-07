<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\TaxCoreLivewire\Livewire\TaxRules;
use Livewire\Livewire;

final class TaxCoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-tax-rules', TaxRules::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-tax-core');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-tax-core');
    }
}
