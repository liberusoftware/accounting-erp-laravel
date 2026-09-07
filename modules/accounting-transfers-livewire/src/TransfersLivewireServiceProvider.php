<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\TransfersLivewire\Livewire\TransfersList;
use Livewire\Livewire;

final class TransfersLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-transfers-list', TransfersList::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-transfers');
    }
}
