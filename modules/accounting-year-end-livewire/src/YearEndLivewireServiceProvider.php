<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEndLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\YearEndLivewire\Livewire\YearEndOverview;
use Livewire\Livewire;

final class YearEndLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-year-end');
        Livewire::component('module-accounting-year-end', YearEndOverview::class);
    }
}
