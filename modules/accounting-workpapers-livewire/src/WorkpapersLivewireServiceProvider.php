<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\WorkpapersLivewire\Livewire\Workpapers;
use Livewire\Livewire;

final class WorkpapersLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-workpapers-list', Workpapers::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-workpapers');
    }
}
