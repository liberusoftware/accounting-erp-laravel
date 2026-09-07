<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\EInvoicingLivewire\Livewire\Documents;
use Livewire\Livewire;

final class EInvoicingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-e-invoicing-livewire');
        Livewire::component('module-accounting-e-invoicing::documents', Documents::class);
        Livewire::component('e-invoicing', Documents::class);
    }
}
