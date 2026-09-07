<?php

declare(strict_types=1);

namespace Liberu\Accounting\VatLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\VatLivewire\Livewire\VatRecords;
use Livewire\Livewire;

final class VatLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-vat-records', VatRecords::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-vat');
    }
}
