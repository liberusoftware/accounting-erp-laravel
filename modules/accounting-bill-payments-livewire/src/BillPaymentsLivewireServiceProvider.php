<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BillPaymentsLivewire\Livewire\Proposals;
use Livewire\Livewire;

final class BillPaymentsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-bill-payments');
        Livewire::component('module-accounting-bill-payments::proposals', Proposals::class);
        Livewire::component('module-accounting-bill-payments-proposals', Proposals::class);
    }
}
