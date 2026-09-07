<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ManagementReportingLivewire\Livewire\ReportPacks;
use Livewire\Livewire;

final class ManagementReportingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-accounting-management-reporting::packs', ReportPacks::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-management-reporting-livewire');
    }
}
