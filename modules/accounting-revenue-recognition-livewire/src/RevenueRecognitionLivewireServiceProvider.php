<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognitionLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\RevenueRecognitionLivewire\Livewire\RevenueSchedules;
use Livewire\Livewire;

final class RevenueRecognitionLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-revenue-schedules', RevenueSchedules::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-revenue-recognition');
    }
}
