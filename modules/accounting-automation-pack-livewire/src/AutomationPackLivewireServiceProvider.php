<?php

declare(strict_types=1);

namespace Liberu\Accounting\AutomationPackLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AutomationPackLivewire\Livewire\Recipes;
use Livewire\Livewire;

final class AutomationPackLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-automation-pack-livewire');
        Livewire::component('accounting-automation-recipes', Recipes::class);
    }
}
