<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesFilament;

use Illuminate\Support\ServiceProvider;

final class JobEstimatesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JobEstimatesFilamentPlugin::class);
    }
}
