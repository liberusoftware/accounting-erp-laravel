<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersFilament;

use Illuminate\Support\ServiceProvider;

final class WorkpapersFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WorkpapersFilamentPlugin::class, fn (): WorkpapersFilamentPlugin => WorkpapersFilamentPlugin::make());
    }
}
