<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ReceiptManagementLivewire\Livewire\Receipts;
use Livewire\Livewire;

final class ReceiptManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-receipts', Receipts::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-receipt-management');
    }
}
