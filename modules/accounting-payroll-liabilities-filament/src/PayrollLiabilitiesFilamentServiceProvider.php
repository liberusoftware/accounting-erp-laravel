<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesFilament;

use Illuminate\Support\ServiceProvider;

final class PayrollLiabilitiesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PayrollLiabilitiesFilamentPlugin::class);
    }
}
