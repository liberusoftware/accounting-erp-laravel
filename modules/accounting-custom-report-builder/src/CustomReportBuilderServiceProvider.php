<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder;

use Illuminate\Support\ServiceProvider;

final class CustomReportBuilderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
