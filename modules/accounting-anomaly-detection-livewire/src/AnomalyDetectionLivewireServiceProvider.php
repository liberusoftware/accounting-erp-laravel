<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AnomalyDetectionLivewire\Livewire\Anomalies;
use Livewire\Livewire;

final class AnomalyDetectionLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-anomaly-detection');
        Livewire::component('accounting-anomalies', Anomalies::class);
    }
}
