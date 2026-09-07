<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\GoodsAndServiceReceiptsLivewire\Livewire\Receipts;
use Livewire\Livewire;

final class GoodsAndServiceReceiptsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-goods-and-service-receipts::receipts', Receipts::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-goods-and-service-receipts-livewire');
    }
}
