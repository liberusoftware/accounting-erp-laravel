<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilderApi;

use Illuminate\Support\ServiceProvider;

final class CustomReportBuilderApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
