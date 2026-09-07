<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsFilament;

use Illuminate\Support\ServiceProvider;

final class ReimbursementsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReimbursementsFilamentPlugin::class);
    }
}
