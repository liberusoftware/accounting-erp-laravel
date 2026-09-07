<?php

declare(strict_types=1);

namespace Liberu\Accounting\IntercompanyLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\IntercompanyLivewire\Livewire\Intercompany;
use Livewire\Livewire;

final class IntercompanyLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-intercompany::transactions', Intercompany::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-intercompany-livewire');
    }
}
