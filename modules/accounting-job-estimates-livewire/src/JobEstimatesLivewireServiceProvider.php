<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\JobEstimatesLivewire\Livewire\JobEstimates;
use Livewire\Livewire;

final class JobEstimatesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-job-estimates::estimates', JobEstimates::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-job-estimates-livewire');
    }
}
