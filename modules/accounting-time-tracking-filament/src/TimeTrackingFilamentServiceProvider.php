<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTrackingFilament;

use Illuminate\Support\ServiceProvider;

final class TimeTrackingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TimeTrackingFilamentPlugin::class, fn (): TimeTrackingFilamentPlugin => TimeTrackingFilamentPlugin::make());
    }
}
