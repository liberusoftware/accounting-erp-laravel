<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CashCodingLivewire\Livewire\Batches;
use Livewire\Livewire;

final class CashCodingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-cash-coding');
        Livewire::component('module-accounting-cash-coding::batches', Batches::class);
        Livewire::component('module-accounting-cash-coding-batches', Batches::class);
    }
}
