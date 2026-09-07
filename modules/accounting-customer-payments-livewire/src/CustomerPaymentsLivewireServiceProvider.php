<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPaymentsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CustomerPaymentsLivewire\Livewire\PaymentOverview;
use Livewire\Livewire;

final class CustomerPaymentsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-customer-payments');
        Livewire::component('module-accounting-customer-payments', PaymentOverview::class);
    }
}
