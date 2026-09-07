<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AssetEventsLivewire\Livewire\AssetEvents;
use Livewire\Livewire;

final class AssetEventsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-asset-events');
        Livewire::component('accounting-asset-events', AssetEvents::class);
    }
}
