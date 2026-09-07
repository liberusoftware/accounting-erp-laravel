<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCardsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CorporateCardsLivewire\Livewire\CardOverview;
use Livewire\Livewire;

final class CorporateCardsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-corporate-cards');
        Livewire::component('module-accounting-corporate-cards', CardOverview::class);
    }
}
