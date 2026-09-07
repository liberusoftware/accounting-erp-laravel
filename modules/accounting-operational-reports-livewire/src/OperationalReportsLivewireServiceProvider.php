<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReportsLivewire;

use Illuminate\Support\ServiceProvider;

final class OperationalReportsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-operational-reports');
    }
}
