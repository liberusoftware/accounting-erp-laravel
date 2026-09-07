<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTrackingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\TimeTrackingLivewire\Livewire\TimeEntries;
use Livewire\Livewire;

final class TimeTrackingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-time-tracking-entries', TimeEntries::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-time-tracking');
    }
}
