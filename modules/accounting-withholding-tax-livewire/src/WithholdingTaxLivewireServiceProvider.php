<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTaxLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\WithholdingTaxLivewire\Livewire\WithholdingTaxRules;
use Livewire\Livewire;

final class WithholdingTaxLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-withholding-tax-rules', WithholdingTaxRules::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-withholding-tax');
    }
}
