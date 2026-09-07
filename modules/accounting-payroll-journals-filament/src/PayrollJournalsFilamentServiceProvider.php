<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsFilament;

use Illuminate\Support\ServiceProvider;

final class PayrollJournalsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PayrollJournalsFilamentPlugin::class);
    }
}
