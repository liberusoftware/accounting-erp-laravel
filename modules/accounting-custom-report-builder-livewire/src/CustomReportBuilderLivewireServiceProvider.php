<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilderLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CustomReportBuilderLivewire\Livewire\ReportOverview;
use Livewire\Livewire;

final class CustomReportBuilderLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-custom-report-builder');
        Livewire::component('module-accounting-custom-report-builder', ReportOverview::class);
    }
}
