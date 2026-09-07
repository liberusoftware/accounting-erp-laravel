<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankFeedsLivewire\Livewire\Connections;
use Livewire\Livewire;

final class BankFeedsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-bank-feeds');
        Livewire::component('module-accounting-bank-feeds::connections', Connections::class);
        Livewire::component('module-accounting-bank-feeds-connections', Connections::class);
    }
}
