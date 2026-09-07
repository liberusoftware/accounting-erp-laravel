<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReportingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ContractorReportingLivewire\Livewire\ReportOverview;
use Livewire\Livewire;

final class ContractorReportingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-contractor-reporting');
        Livewire::component('module-accounting-contractor-reporting', ReportOverview::class);
    }
}
