<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\EstimatesAndQuotesLivewire\Livewire\Estimates;
use Livewire\Livewire;

final class EstimatesAndQuotesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-estimates-and-quotes-livewire');
        Livewire::component('module-accounting-estimates-and-quotes::estimates', Estimates::class);
        Livewire::component('estimates-and-quotes', Estimates::class);
    }
}
