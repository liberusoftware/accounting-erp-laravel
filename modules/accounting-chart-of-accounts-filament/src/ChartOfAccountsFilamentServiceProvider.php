<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsFilament;

use Illuminate\Support\ServiceProvider;

final class ChartOfAccountsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChartOfAccountsFilamentPlugin::class);
    }
}
