<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ThreeWayMatchingLivewire\Livewire\Matches;
use Livewire\Livewire;

final class ThreeWayMatchingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-three-way-matching');
        Livewire::component('module-accounting-three-way-matching-matches', Matches::class);
    }
}
